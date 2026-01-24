<?php

namespace App\Http\Controllers\File;

use App\Models\Upload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;

class FileController extends Controller
{
    // Maximum file size in bytes (100MB)
    private const MAX_FILE_SIZE = 100 * 1024 * 1024;
    
    // Maximum total upload size in bytes (500MB)
    private const MAX_TOTAL_SIZE = 500 * 1024 * 1024;

    public function upload(Request $request)
    {
        // Validate request
        if (!$request->hasFile('files')) {
            return response()->json(['error' => 'No files were uploaded.'], 400);
        }
    
        $files = $request->file('files');
        $files = is_array($files) ? $files : [$files];

        // Optional auth (if bearer token is provided)
        $user = auth('sanctum')->user();
        $ip = $request->ip();

        // Daily limits:
        // - Guests: 3 files/day per IP
        // - Signed in: 5 files/day per account
        $dailyLimit = $user ? 5 : 3;
        $newFileCount = count($files);

        $start = now()->startOfDay();
        $end = now()->endOfDay();
        $usedToday = $user
            ? (int) Upload::query()
                ->where('user_id', $user->id)
                ->whereBetween('created_at', [$start, $end])
                ->sum('file_count')
            : (int) Upload::query()
                ->whereNull('user_id')
                ->where('ip', $ip)
                ->whereBetween('created_at', [$start, $end])
                ->sum('file_count');

        if ($usedToday + $newFileCount > $dailyLimit) {
            return response()->json([
                'error' => $user
                    ? "Daily limit reached. Accounts can upload up to {$dailyLimit} file(s) per day."
                    : "Daily limit reached. IPs can upload up to {$dailyLimit} file(s) per day.",
                'limit' => $dailyLimit,
                'used' => $usedToday,
                'requested' => $newFileCount,
            ], 429);
        }
    
        // Validate each file
        $totalSize = 0;
        foreach ($files as $file) {
            if (!$file->isValid()) {
                return response()->json(['error' => 'One or more files are invalid.'], 400);
            }
    
            if ($file->getSize() > self::MAX_FILE_SIZE) {
                return response()->json(['error' => 'File too large: ' . $file->getClientOriginalName()], 400);
            }
    
            $totalSize += $file->getSize();
            if ($totalSize > self::MAX_TOTAL_SIZE) {
                return response()->json(['error' => 'Total upload size exceeds limit.'], 400);
            }
        }
    
        // Create zip file (fast path: add directly from PHP upload temp files)
        $zipFileName = (string) Str::uuid() . '.zip';
        $zipsDir = storage_path('app/private/zips');
        $zipPath = "{$zipsDir}/{$zipFileName}";

        if (!is_dir($zipsDir) && !mkdir($zipsDir, 0777, true) && !is_dir($zipsDir)) {
            Log::error("Failed to create zips directory: {$zipsDir}");
            return response()->json(['error' => 'Server storage error.'], 500);
        }

        $zip = new ZipArchive();
        $result = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($result !== TRUE) {
            Log::error("ZipArchive open failed: $result, Path: $zipPath");
            return response()->json(['error' => 'Failed to create zip file.'], 500);
        }

        $usedNames = [];
        foreach ($files as $file) {
            $original = $file->getClientOriginalName() ?: 'file';
            $baseName = $this->sanitizeFilename($original);
            $finalName = $baseName;

            // Ensure unique names inside the zip
            $i = 1;
            while (isset($usedNames[$finalName])) {
                $dot = strrpos($baseName, '.');
                if ($dot === false) {
                    $finalName = "{$baseName} ({$i})";
                } else {
                    $name = substr($baseName, 0, $dot);
                    $ext = substr($baseName, $dot);
                    $finalName = "{$name} ({$i}){$ext}";
                }
                $i++;
            }
            $usedNames[$finalName] = true;

            $zip->addFile($file->getRealPath(), $finalName);

            // Speed: store without compression (fastest). Swap to CM_DEFLATE if you want smaller zips.
            if (method_exists($zip, 'setCompressionName')) {
                $zip->setCompressionName($finalName, ZipArchive::CM_STORE);
            }
        }

        $zip->close();

        $expiresAt = now()->addDay();

        Upload::create([
            'user_id' => $user?->id,
            'ip' => $ip,
            'file_count' => $newFileCount,
            'zip_name' => $zipFileName,
            'zip_path' => $zipPath,
            'expires_at' => $expiresAt,
        ]);

        $downloadUrl = url("api/download/{$zipFileName}");
        return response()->json([
            'download_url' => $downloadUrl,
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);
    }

    public function download($filename)
    {
        // Validate filename
        if (!preg_match('/^[a-f0-9\-]{36}\.zip$/i', $filename)) {
            return response()->json(['error' => 'Invalid file name.'], 400);
        }

        /** @var \App\Models\Upload|null $upload */
        $upload = Upload::query()->where('zip_name', $filename)->first();
        if (!$upload) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        if ($upload->expires_at && now()->greaterThan($upload->expires_at)) {
            if ($upload->zip_path && file_exists($upload->zip_path)) {
                @unlink($upload->zip_path);
            }
            return response()->json(['error' => 'This download link has expired.'], 410);
        }

        $filePath = $upload->zip_path;
        if (!$filePath || !file_exists($filePath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        $upload->forceFill(['downloaded_at' => now()])->save();

        return response()
            ->download($filePath, $filename, ['Content-Type' => 'application/zip'])
            ->deleteFileAfterSend(true);
    }

    /**
     * Sanitize filename to prevent directory traversal and other attacks
     */
    private function sanitizeFilename($filename)
    {
        // Remove path information
        $filename = basename($filename);
        
        // Replace potentially dangerous characters
        $filename = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", $filename);
        
        // Limit filename length
        return substr($filename, 0, 255);
    }
}