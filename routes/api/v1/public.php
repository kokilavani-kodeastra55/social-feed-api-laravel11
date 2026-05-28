<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes (V1)
|--------------------------------------------------------------------------
*/

Route::get('/status', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is reachable.',
        'data' => [
            'version' => 'v1',
        ],
        'errors' => null,
    ]);
});

Route::prefix('auth')->group(function (): void {
    // POST /api/v1/auth/login
});
