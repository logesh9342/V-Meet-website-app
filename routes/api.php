<?php

use Illuminate\Support\Facades\Route;

// Keep stateless APIs here only if they don't require session auth.
// For web-authenticated features (chat, meetings), routes are defined in routes/web.php.

// Example: file sharing list/create via API (adjust middleware as needed)
Route::get('/files', [\App\Http\Controllers\FileShareController::class, 'index']);
Route::post('/files', [\App\Http\Controllers\FileShareController::class, 'store']);
