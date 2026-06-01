<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LikeService;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    protected LikeService $likeService;

    public function __construct(LikeService $likeService)
    {
        $this->likeService = $likeService;
    }

    /**
     * Toggle like/unlike on a post.
     */
    public function togglePostLike(Post $post): JsonResponse
    {
        try {
            $result = $this->likeService->togglePostLike($post, auth()->id());
            $message = $result['liked'] ? 'Post liked.' : 'Post unliked.';

            return api_success($result, $message);
        } catch (\Throwable $e) {
            Log::error('Error toggling post like: ' . $e->getMessage());
            return api_error('Failed to toggle like on post.', null, 500);
        }
    }

    /**
     * Toggle like/unlike on a comment.
     */
    public function toggleCommentLike(Comment $comment): JsonResponse
    {
        try {
            $result = $this->likeService->toggleCommentLike($comment, auth()->id());
            $message = $result['liked'] ? 'Comment liked.' : 'Comment unliked.';

            return api_success($result, $message);
        } catch (\Throwable $e) {
            Log::error('Error toggling comment like: ' . $e->getMessage());
            return api_error('Failed to toggle like on comment.', null, 500);
        }
    }
}
