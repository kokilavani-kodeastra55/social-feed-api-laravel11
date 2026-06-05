<?php

namespace App\Repositories;

use App\Models\Like;
use Illuminate\Database\Eloquent\Model;

class LikeRepository
{
    /**
     * Find a user's like on (Post or Comment).
     */
    public function findLikeForModel(Model $model, int $userId): ?Like
    {
        return $model->likes()->where('user_id', $userId)->first();
    }

    /**
     * Delete a like.
     */
    public function delete(Like $like): bool
    {
        return $like->delete();
    }

    /**
     * Create a like.
     */
    public function createForModel(Model $model, int $userId): Like
    {
        return $model->likes()->create(['user_id' => $userId]);
    }

    /**
     * Get count of likes.
     */
    public function countForModel(Model $model): int
    {
        return $model->likes()->count();
    }
}
