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
        return Comment::query()->find($id);
    }

    /**
     * Create a new comment.
     */
    public function create(array $data): Comment
    {
        return Comment::query()->create($data);
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
     * Delete a comment.
     */
    public function delete(Comment $comment): bool
    {
        return (bool) $comment->delete();
    }
}
