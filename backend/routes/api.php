<?php

use App\Http\Controllers\File\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/upload', [FileController::class, 'upload']);
Route::get('/download/{filename}', [FileController::class, 'download']);

