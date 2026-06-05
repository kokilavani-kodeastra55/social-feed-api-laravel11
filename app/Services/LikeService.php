<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Post;
use App\Repositories\LikeRepository;
use Illuminate\Database\Eloquent\Model;

class LikeService
{
    protected LikeRepository $likeRepository;

    public function __construct(LikeRepository $likeRepository)
    {
        $this->likeRepository = $likeRepository;
    }

    /**
     * Toggle like on post.
     */
    public function togglePostLike(Post $post, int $userId): array
    {
        return $this->toggleLike($post, $userId);
    }

    /**
     * Toggle like on comment.
     */
    public function toggleCommentLike(Comment $comment, int $userId): array
    {
        return $this->toggleLike($comment, $userId);
    }

    /**
     * Toggle like on reply.
     */
    public function toggleReplyLike(CommentReply $reply, int $userId): array
    {
        return $this->toggleLike($reply, $userId);
    }

    /**
     * Private helper to toggle like.
     */
    private function toggleLike(Model $model, int $userId): array
    {
        $like = $this->likeRepository->findLikeForModel($model, $userId);

        if ($like) {
            $this->likeRepository->delete($like);
            $liked = false;
        } else {
            $this->likeRepository->createForModel($model, $userId);
            $liked = true;
        }

        return [
            'liked' => $liked,
            'likes_count' => $this->likeRepository->countForModel($model),
        ];
    }
}
