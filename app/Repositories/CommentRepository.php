<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentRepository
{
    /**
     * Find a comment by ID.
     */
    public function findById(int $id): ?Comment
    {
        return Comment::find($id);
    }

    /**
     * Create a new comment.
     */
    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    /**
     * Create a new comment reply.
     */
    public function createReply(array $data): \App\Models\CommentReply
    {
        return \App\Models\CommentReply::create($data);
    }

    /**
     * Update an existing comment.
     */
    public function update(Comment $comment, array $data): Comment
    {
        $comment->update($data);
        return $comment;
    }

    /**
     * Update an existing comment reply.
     */
    public function updateReply(\App\Models\CommentReply $reply, array $data): \App\Models\CommentReply
    {
        $reply->update($data);
        return $reply;
    }

    /**
     * Delete a comment.
     */
    public function delete(Comment $comment): bool
    {
        return $comment->delete();
    }

    /**
     * Delete a comment reply.
     */
    public function deleteReply(\App\Models\CommentReply $reply): bool
    {
        return $reply->delete();
    }
}
