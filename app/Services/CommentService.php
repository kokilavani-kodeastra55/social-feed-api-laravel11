<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Post;
use App\Repositories\CommentRepository;
use App\Repositories\CommentReplyRepository;

class CommentService
{
    protected CommentRepository $commentRepository;
    protected CommentReplyRepository $commentReplyRepository;

    public function __construct(
        CommentRepository $commentRepository,
        CommentReplyRepository $commentReplyRepository
    ) {
        $this->commentRepository = $commentRepository;
        $this->commentReplyRepository = $commentReplyRepository;
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
        ]);
    }

    /**
     * Create nested reply.
     */
    public function createReply(Comment $comment, array $data, int $userId): CommentReply
    {
        return $this->commentReplyRepository->create([
            'comment_id' => $comment->id,
            'user_id' => $userId,
            'comment' => $data['comment'],
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
