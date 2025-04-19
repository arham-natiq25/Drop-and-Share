<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class FileController extends Controller
{
    // Maximum file size in bytes (100MB)
    private const MAX_FILE_SIZE = 100 * 1024 * 1024;
    
    // Maximum total upload size in bytes (500MB)
    private const MAX_TOTAL_SIZE = 500 * 1024 * 1024;
    
    // Allowed file types
    private const ALLOWED_MIME_TYPES = [
        // Images
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        
        // Documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        
        // Archives
        'application/zip',
        'application/x-rar-compressed',
        'application/x-7z-compressed',
        'application/x-tar',
        'application/gzip',
        
        // Audio/Video
        'audio/mpeg',
        'audio/wav',
        'video/mp4',
        'video/webm',
        'video/quicktime',
        
        // Code
        'text/html',
        'text/css',
        'text/javascript',
        'application/json',
        'application/xml',
    ];

    public function upload(Request $request)
    {
        // Validate request
        if (!$request->hasFile('files')) {
            return response()->json(['error' => 'No files were uploaded.'], 400);
        }
    
        $files = $request->file('files');
    
        // Validate each file
        $totalSize = 0;
        foreach ($files as $file) {
            if (!$file->isValid()) {
                return response()->json(['error' => 'One or more files are invalid.'], 400);
            }
    
            if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
                return response()->json(['error' => 'File type not allowed: ' . $file->getClientOriginalName()], 400);
            }
    
            if ($file->getSize() > self::MAX_FILE_SIZE) {
                return response()->json(['error' => 'File too large: ' . $file->getClientOriginalName()], 400);
            }
    
            $totalSize += $file->getSize();
            if ($totalSize > self::MAX_TOTAL_SIZE) {
                return response()->json(['error' => 'Total upload size exceeds limit.'], 400);
            }
        }
    
        // Create unique folder and zip file name
        $folderName = Str::uuid();
        $zipFileName = $folderName . '.zip';
        $zipsDir = storage_path('app/public/zips');
        $zipPath = "{$zipsDir}/{$zipFileName}";
    
        // Ensure the zips directory exists physically
        if (!file_exists($zipsDir)) {
            mkdir($zipsDir, 0777, true);
        }
    
        // Create zip file
        $zip = new ZipArchive();
        $result = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($result !== TRUE) {
            Log::error("ZipArchive open failed: $result, Path: $zipPath");
            return response()->json(['error' => 'Failed to create zip file.'], 500);
        }
    
        // Process files in chunks
        $chunkSize = 10;
        $fileChunks = array_chunk($files, $chunkSize);
    
        $tempDir = storage_path("app/temp/{$folderName}");
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
    
        foreach ($fileChunks as $chunk) {
            foreach ($chunk as $file) {
                $fileName = $this->sanitizeFilename($file->getClientOriginalName());
                $file->move($tempDir, $fileName);
                $zip->addFile("{$tempDir}/{$fileName}", $fileName);
            }
        }
    
        $zip->close();
    
        // Now cleanup temp files
        $this->deleteDirectory($tempDir);
    
        $downloadUrl = url("api/download/{$zipFileName}");
        return response()->json([
            'download_url' => $downloadUrl,
            'expires_at' => now()->addHours(24)->toDateTimeString()
        ]);
    }

    public function download($filename)
    {
        // Validate filename
        if (!preg_match('/^[a-f0-9\-]{36}\.zip$/i', $filename)) {
            return response()->json(['error' => 'Invalid file name.'], 400);
        }

        $filePath = storage_path("app/public/zips/{$filename}");

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        // Stream the file for better memory usage
        return response()->streamDownload(function () use ($filePath) {
            $stream = fopen($filePath, 'rb');
            fpassthru($stream);
            fclose($stream);
            
            // Delete file after download
            unlink($filePath);
        }, $filename, [
            'Content-Type' => 'application/zip',
            'Content-Length' => filesize($filePath),
        ]);
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

    /**
     * Recursively delete a directory
     */
    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        
        rmdir($dir);
    }
}