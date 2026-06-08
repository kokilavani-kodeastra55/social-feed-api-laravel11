<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\CommentReply;
use App\Services\LikeService;
use App\Helpers\HTTPResponse;
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
     * Toggle like on post.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function togglePostLike(Post $post): JsonResponse
    {
        try {
            $userId = auth()->id();
            $result = $this->likeService->togglePostLike($post, $userId);
            $message = $result['liked'] ? 'Post liked successfully.' : 'Post unliked successfully.';

            return HTTPResponse::ok($result, $message);
        } catch (\Throwable $e) {
            Log::error('Error toggling post like: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to toggle post like.');
        }
    }

    /**
     * Toggle like on comment.
     *
     * @param Comment $comment
     * @return JsonResponse
     */
    public function toggleCommentLike(Comment $comment): JsonResponse
    {
        try {
            $userId = auth()->id();
            $result = $this->likeService->toggleCommentLike($comment, $userId);
            $message = $result['liked'] ? 'Comment liked successfully.' : 'Comment unliked successfully.';

            return HTTPResponse::ok($result, $message);
        } catch (\Throwable $e) {
            Log::error('Error toggling comment like: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to toggle comment like.');
        }
    }

    /**
     * Toggle like on reply.
     *
     * @param CommentReply $reply
     * @return JsonResponse
     */
    public function toggleReplyLike(CommentReply $reply): JsonResponse
    {
        try {
            $userId = auth()->id();
            $result = $this->likeService->toggleReplyLike($reply, $userId);
            $message = $result['liked'] ? 'Reply liked successfully.' : 'Reply unliked successfully.';

            return HTTPResponse::ok($result, $message);
        } catch (\Throwable $e) {
            Log::error('Error toggling reply like: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to toggle reply like.');
        }
    }
}
