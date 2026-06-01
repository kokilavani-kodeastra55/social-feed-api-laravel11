<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\LikeController;
use App\Http\Controllers\Api\V1\FeedController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication Route (V1 / Postman Login expects /api/login)
Route::post('/login', [AuthController::class, 'login']);

// Authenticated Routes at /api/
Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// V1 Prefix Group (/api/v1/...)
Route::prefix('v1')->group(function (): void {
    // Public Status Route
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

    // Protected Routes inside V1 Group
    Route::middleware('auth:sanctum')->group(function (): void {
        // Authenticated profile details
        Route::get('/auth/profile', function (Request $request) {
            return api_success([
                'user' => $request->user(),
            ], 'Authenticated route access granted.');
        });

        // Posts Resources
        Route::apiResource('posts', PostController::class);

        // Comments & Replies
        Route::post('posts/{post}/comments', [CommentController::class, 'store']);
        Route::post('comments/{comment}/reply', [CommentController::class, 'reply']);
        Route::put('comments/{comment}', [CommentController::class, 'update']);
        Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

        // Likes Toggle (Polymorphic)
        Route::post('posts/{post}/like', [LikeController::class, 'togglePostLike']);
        Route::post('comments/{comment}/like', [LikeController::class, 'toggleCommentLike']);

        // Feed chronologically or popular ranked
        Route::get('feed', [FeedController::class, 'index']);
    });
});
