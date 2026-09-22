<?php

use App\Http\Controllers\File\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'app' => config('app.name'),
    'time' => now()->toIso8601String(),
]));

// Public endpoint: throttle it so one client cannot fill the disk.
Route::post('/upload', [FileController::class, 'upload'])
    ->middleware('throttle:20,1');

Route::get('/share/{filename}', [FileController::class, 'show'])
    ->where('filename', '[A-Za-z0-9\-]+\.zip');

Route::get('/download/{filename}', [FileController::class, 'download'])
    ->middleware('throttle:60,1')
    ->where('filename', '[A-Za-z0-9\-]+\.zip');
