<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        ini_set('memory_limit', '2G');

        if (!$request->hasFile('files')) {
            return response()->json(['error' => 'No files were uploaded.'], 400);
        }

        $files = $request->file('files');
        $folderName = Str::uuid();
        $zipFileName = $folderName . '.zip';

        $zip = new ZipArchive();
        $zipDir = storage_path("app/public/zips");

        if (!file_exists($zipDir)) {
            mkdir($zipDir, 0777, true);
        }
        $zipPath = "{$zipDir}/{$zipFileName}";

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($files as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = $file->storeAs("uploads/{$folderName}", $fileName, 'public');
                $zip->addFile(storage_path("app/public/{$filePath}"), $fileName);
            }
            $zip->close();
        } else {
            return response()->json(['error' => 'Failed to create zip file.'], 500);
        }

        // Clean up original files
        Storage::deleteDirectory("public/uploads/{$folderName}");

        $downloadUrl = url("api/download/{$zipFileName}");
        return response()->json(['download_url' => $downloadUrl]);
    }

    public function download($filename)
    {
        $filePath = storage_path("app/public/zips/{$filename}");

        if (file_exists($filePath)) {
            return response()->download($filePath)->deleteFileAfterSend(true);
        } else {
            return response()->json(['error' => 'File not found.'], 404);
        }
    }
}
