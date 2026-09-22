<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Models\Share;
use App\Models\ShareFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class FileController extends Controller
{
    /**
     * Accept a batch of files, zip them, persist a Share row and hand back
     * both the shareable page link and the direct download link.
     */
    public function upload(Request $request): JsonResponse
    {
        if (! $request->hasFile('files')) {
            return response()->json(['error' => 'No files were uploaded.'], 400);
        }

        $files = $request->file('files');

        if (! is_array($files)) {
            $files = [$files];
        }

        $maxFiles = config('dropnshare.max_files');
        if (count($files) > $maxFiles) {
            return response()->json([
                'error' => "You can upload at most {$maxFiles} files at once.",
            ], 422);
        }

        $allowedMimes = config('dropnshare.allowed_mime_types');
        $maxFileSize = config('dropnshare.max_file_size');
        $maxTotalSize = config('dropnshare.max_total_size');

        // Validate everything up front so we never leave a half-written zip behind.
        $totalSize = 0;
        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                return response()->json(['error' => 'One or more files are invalid.'], 400);
            }

            if (! in_array($file->getMimeType(), $allowedMimes, true)) {
                return response()->json([
                    'error' => 'File type not allowed: '.$file->getClientOriginalName(),
                ], 422);
            }

            if ($file->getSize() > $maxFileSize) {
                return response()->json([
                    'error' => 'File too large: '.$file->getClientOriginalName()
                        .' (max '.$this->humanSize($maxFileSize).' per file).',
                ], 422);
            }

            $totalSize += $file->getSize();
            if ($totalSize > $maxTotalSize) {
                return response()->json([
                    'error' => 'Total upload size exceeds the '
                        .$this->humanSize($maxTotalSize).' limit.',
                ], 422);
            }
        }

        $uuid = (string) Str::uuid();
        $zipFileName = $uuid.'.zip';
        $zipsDir = config('dropnshare.zips_path');
        $zipPath = $zipsDir.'/'.$zipFileName;
        $tempDir = config('dropnshare.temp_path').'/'.$uuid;

        $this->ensureDirectory($zipsDir);
        $this->ensureDirectory($tempDir);

        $zip = new ZipArchive();
        $opened = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($opened !== true) {
            Log::error('ZipArchive open failed', ['code' => $opened, 'path' => $zipPath]);
            $this->deleteDirectory($tempDir);

            return response()->json(['error' => 'Failed to create zip file.'], 500);
        }

        // Track names already used inside the archive so two files called
        // "photo.jpg" don't silently overwrite each other.
        $usedNames = [];
        $records = [];

        try {
            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();
                $safeName = $this->uniqueName($this->sanitizeFilename($originalName), $usedNames);

                $size = $file->getSize();
                $mime = $file->getMimeType();

                $file->move($tempDir, $safeName);

                if (! $zip->addFile($tempDir.'/'.$safeName, $safeName)) {
                    throw new \RuntimeException("Could not add {$safeName} to the archive.");
                }

                $records[] = [
                    'original_name' => $originalName,
                    'stored_name' => $safeName,
                    'mime_type' => $mime,
                    'size' => $size,
                ];
            }

            if (! $zip->close()) {
                throw new \RuntimeException('Failed to finalise the archive.');
            }
        } catch (\Throwable $e) {
            Log::error('Upload failed while building archive', ['error' => $e->getMessage()]);

            @$zip->close();
            $this->deleteDirectory($tempDir);
            if (is_file($zipPath)) {
                @unlink($zipPath);
            }

            return response()->json(['error' => 'Failed to package your files. Please try again.'], 500);
        }

        // The originals only ever existed to be zipped.
        $this->deleteDirectory($tempDir);

        $expiresAt = now()->addHours(config('dropnshare.expiry_hours'));

        try {
            $share = DB::transaction(function () use ($uuid, $zipFileName, $totalSize, $records, $expiresAt, $request) {
                $share = Share::create([
                    'uuid' => $uuid,
                    'zip_filename' => $zipFileName,
                    'total_size' => $totalSize,
                    'file_count' => count($records),
                    'expires_at' => $expiresAt,
                    'ip_address' => $request->ip(),
                    'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
                ]);

                foreach ($records as $record) {
                    $share->files()->create($record);
                }

                return $share;
            });
        } catch (\Throwable $e) {
            Log::error('Could not persist share', ['error' => $e->getMessage()]);

            if (is_file($zipPath)) {
                @unlink($zipPath);
            }

            return response()->json(['error' => 'Could not save your upload. Please try again.'], 500);
        }

        return response()->json([
            'uuid' => $share->uuid,
            'filename' => $share->zip_filename,
            'share_url' => $share->shareUrl(),
            'download_url' => $share->downloadUrl(),
            'file_count' => $share->file_count,
            'total_size' => $share->total_size,
            'total_size_human' => $this->humanSize($share->total_size),
            'expires_at' => $share->expires_at->toDateTimeString(),
        ], 201);
    }

    /**
     * Metadata for a share, so the download page can show what it is about
     * to fetch without pulling the whole archive down first.
     */
    public function show(string $filename): JsonResponse
    {
        $share = $this->resolveShare($filename);

        if ($share instanceof JsonResponse) {
            return $share;
        }

        return response()->json([
            'uuid' => $share->uuid,
            'filename' => $share->zip_filename,
            'file_count' => $share->file_count,
            'total_size' => $share->total_size,
            'total_size_human' => $this->humanSize($share->total_size),
            'download_count' => $share->download_count,
            'expires_at' => $share->expires_at?->toDateTimeString(),
            'created_at' => $share->created_at->toDateTimeString(),
            'files' => $share->files->map(fn (ShareFile $file) => [
                'name' => $file->original_name,
                'size' => $file->size,
                'size_human' => $this->humanSize($file->size),
                'mime_type' => $file->mime_type,
            ])->all(),
        ]);
    }

    /**
     * Stream the archive. The link stays usable until it expires, so it can
     * genuinely be shared with more than one person.
     */
    public function download(string $filename): StreamedResponse|JsonResponse
    {
        $share = $this->resolveShare($filename);

        if ($share instanceof JsonResponse) {
            return $share;
        }

        $path = $share->zipPath();
        $size = filesize($path);

        $share->forceFill([
            'download_count' => $share->download_count + 1,
            'last_downloaded_at' => now(),
        ])->save();

        return response()->streamDownload(function () use ($path) {
            $stream = fopen($path, 'rb');

            if ($stream === false) {
                return;
            }

            fpassthru($stream);
            fclose($stream);
        }, $share->zip_filename, [
            'Content-Type' => 'application/zip',
            'Content-Length' => $size,
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Look a share up by its zip filename, returning a JSON error response
     * when it is missing, expired or already purged.
     */
    private function resolveShare(string $filename): Share|JsonResponse
    {
        if (! preg_match('/^[a-f0-9\-]{36}\.zip$/i', $filename)) {
            return response()->json(['error' => 'Invalid file name.'], 400);
        }

        $share = Share::with('files')->where('zip_filename', $filename)->first();

        if (! $share || $share->deleted_at !== null) {
            return response()->json(['error' => 'This link no longer exists.'], 404);
        }

        if ($share->isExpired()) {
            $share->purge();

            return response()->json([
                'error' => 'This link has expired.',
                'expired' => true,
            ], 410);
        }

        if (! is_file($share->zipPath())) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        return $share;
    }

    private function ensureDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }

    /**
     * Strip path information and anything that could escape the archive.
     */
    private function sanitizeFilename(string $filename): string
    {
        $filename = basename(str_replace('\\', '/', $filename));
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename) ?? '';
        $filename = ltrim($filename, '.');

        if ($filename === '') {
            $filename = 'file';
        }

        return substr($filename, 0, 200);
    }

    /**
     * Append -1, -2, ... when a name is already taken inside the archive.
     */
    private function uniqueName(string $name, array &$used): string
    {
        if (! isset($used[strtolower($name)])) {
            $used[strtolower($name)] = true;

            return $name;
        }

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $base = pathinfo($name, PATHINFO_FILENAME);
        $suffix = $extension === '' ? '' : '.'.$extension;

        $i = 1;
        do {
            $candidate = $base.'-'.$i.$suffix;
            $i++;
        } while (isset($used[strtolower($candidate)]));

        $used[strtolower($candidate)] = true;

        return $candidate;
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 Bytes';
        }

        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);

        return round($bytes / (1024 ** $power), 2).' '.$units[$power];
    }

    private function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        foreach (array_diff(scandir($dir) ?: [], ['.', '..']) as $entry) {
            $path = $dir.'/'.$entry;
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }

        @rmdir($dir);
    }
}
