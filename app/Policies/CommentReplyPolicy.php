<?php

namespace App\Policies;

use App\Models\CommentReply;
use App\Models\User;

class CommentReplyPolicy
{
    /**
     * Determine if the user can update the comment reply.
     */
    public function update(User $user, CommentReply $reply): bool
    {
        return $user->id === $reply->user_id;
    }

    /**
     * Determine if the user can delete the comment reply.
     */
    public function delete(User $user, CommentReply $reply): bool
    {
        return $user->id === $reply->user_id;
    }
}
