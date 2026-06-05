<?php

namespace App\Repositories;

use App\Models\CommentReply;

class CommentReplyRepository
{
    /**
     * Find a comment reply by ID.
     */
    public function findById(int $id): ?CommentReply
    {
        return CommentReply::find($id);
    }

    /**
     * Create a new comment reply.
     */
    public function create(array $data): CommentReply
    {
        return CommentReply::create($data);
    }

    /**
     * Update an existing comment reply.
     */
    public function update(CommentReply $reply, array $data): CommentReply
    {
        $reply->update($data);
        return $reply;
    }

    /**
     * Delete a comment reply.
     */
    public function delete(CommentReply $reply): bool
    {
        return $reply->delete();
    }
}
