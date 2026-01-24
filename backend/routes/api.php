<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\File\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

Route::post('/upload', [FileController::class, 'upload']);
Route::get('/download/{filename}', [FileController::class, 'download']);

