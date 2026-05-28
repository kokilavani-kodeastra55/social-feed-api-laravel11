<?php

use App\Support\Api\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Protected API Routes (V1)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::get('/profile', function (Request $request) {
            return ApiResponder::success([
                'user' => $request->user(),
            ], 'Authenticated route access granted.');
        });
    });

    Route::prefix('posts')->group(function (): void {
        // Social feed post routes
    });

    Route::prefix('comments')->group(function (): void {
        // Comment and reply routes
    });

    Route::prefix('likes')->group(function (): void {
        // Like toggle routes for posts/comments
    });

    Route::prefix('feed')->group(function (): void {
        // GET /api/v1/feed
    });
});
