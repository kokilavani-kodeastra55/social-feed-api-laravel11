<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CommentService
{
    /**
     * Create root comment on post.
     *
     * @param Post $post
     * @param array $data
     * @param int $userId
     * @return Comment
     */
    public function createComment(Post $post, array $data, int $userId): Comment
    {
        try {
            $comment = Comment::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'comment' => $data['comment'],
            ]);

            $this->clearFeedCache();

            return $comment;
        } catch (\Throwable $e) {
            Log::error('CommentService createComment error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Create nested reply.
     *
     * @param Comment $comment
     * @param array $data
     * @param int $userId
     * @return CommentReply
     */
    public function createReply(Comment $comment, array $data, int $userId): CommentReply
    {
        try {
            $reply = CommentReply::create([
                'comment_id' => $comment->id,
                'user_id' => $userId,
                'comment' => $data['comment'],
            ]);

            $this->clearFeedCache();

            return $reply;
        } catch (\Throwable $e) {
            Log::error('CommentService createReply error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Update comment.
     *
     * @param Comment $comment
     * @param array $data
     * @return Comment
     */
    public function updateComment(Comment $comment, array $data): Comment
    {
        try {
            $comment->update($data);

            $this->clearFeedCache();

            return $comment;
        } catch (\Throwable $e) {
            Log::error('CommentService updateComment error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Delete comment.
     *
     * @param Comment $comment
     * @return bool
     */
    public function deleteComment(Comment $comment): bool
    {
        try {
            $deleted = $comment->delete();

            if ($deleted) {
                $this->clearFeedCache();
            }

            return $deleted;
        } catch (\Throwable $e) {
            Log::error('CommentService deleteComment error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Update reply.
     *
     * @param CommentReply $reply
     * @param array $data
     * @return CommentReply
     */
    public function updateReply(CommentReply $reply, array $data): CommentReply
    {
        try {
            $reply->update($data);

            $this->clearFeedCache();

            return $reply;
        } catch (\Throwable $e) {
            Log::error('CommentService updateReply error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Delete reply.
     *
     * @param CommentReply $reply
     * @return bool
     */
    public function deleteReply(CommentReply $reply): bool
    {
        try {
            $deleted = $reply->delete();

            if ($deleted) {
                $this->clearFeedCache();
            }

            return $deleted;
        } catch (\Throwable $e) {
            Log::error('CommentService deleteReply error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Invalidate feed caches.
     *
     * @return void
     */
    protected function clearFeedCache(): void
    {
        try {
            Cache::rememberForever('feed_version', fn() => 1);
            Cache::increment('feed_version');
        } catch (\Throwable $e) {
            Log::error('CommentService clearFeedCache error: ' . $e->getMessage(), ['exception' => $e]);
        }
    }
}
