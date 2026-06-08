<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LikeService
{
    /**
     * Toggle like on post.
     *
     * @param Post $post
     * @param int $userId
     * @return array
     */
    public function togglePostLike(Post $post, int $userId): array
    {
        return $this->toggleLike($post, $userId);
    }

    /**
     * Toggle like on comment.
     *
     * @param Comment $comment
     * @param int $userId
     * @return array
     */
    public function toggleCommentLike(Comment $comment, int $userId): array
    {
        return $this->toggleLike($comment, $userId);
    }

    /**
     * Toggle like on reply.
     *
     * @param CommentReply $reply
     * @param int $userId
     * @return array
     */
    public function toggleReplyLike(CommentReply $reply, int $userId): array
    {
        return $this->toggleLike($reply, $userId);
    }

    /**
     * Private helper to toggle like.
     *
     * @param Model $model
     * @param int $userId
     * @return array
     */
    private function toggleLike(Model $model, int $userId): array
    {
        try {
            $like = Like::findLikeForModel($model, $userId);

            if ($like) {
                $like->delete();
                $liked = false;
            } else {
                Like::createForModel($model, $userId);
                $liked = true;
            }

            Cache::rememberForever('feed_version', fn() => 1);
            Cache::increment('feed_version');

            return [
                'liked' => $liked,
                'likes_count' => Like::countForModel($model),
            ];
        } catch (\Throwable $e) {
            Log::error('LikeService toggleLike error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }
}
