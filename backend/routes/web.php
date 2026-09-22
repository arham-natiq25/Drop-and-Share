<?php

use Illuminate\Support\Facades\Route;

// This host only serves the API; send anyone who lands on it to the app.
Route::get('/', fn () => redirect()->away(config('dropnshare.frontend_url')));
