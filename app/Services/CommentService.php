<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Repositories\CommentRepository;

class CommentService
{
    protected CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * Create root comment on post.
     */
    public function createComment(Post $post, array $data, int $userId): Comment
    {
        return $this->commentRepository->create([
            'post_id' => $post->id,
            'user_id' => $userId,
            'comment' => $data['comment'],
            'parent_id' => null,
        ]);
    }

    /**
     * Create nested reply.
     */
    public function createReply(Comment $comment, array $data, int $userId): Comment
    {
        return $this->commentRepository->create([
            'post_id' => $comment->post_id,
            'user_id' => $userId,
            'comment' => $data['comment'],
            'parent_id' => $comment->id,
        ]);
    }

    /**
     * Update comment.
     */
    public function updateComment(Comment $comment, array $data): Comment
    {
        return $this->commentRepository->update($comment, $data);
    }

    /**
     * Delete comment.
     */
    public function deleteComment(Comment $comment): bool
    {
        return $this->commentRepository->delete($comment);
    }
}
