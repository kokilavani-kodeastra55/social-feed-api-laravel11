<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
    ];

    /**
     * Find a user's like on a given model.
     */
    public static function findLikeForModel(\Illuminate\Database\Eloquent\Model $model, int $userId): ?Like
    {
        return $model->likes()->where('user_id', $userId)->first();
    }

    /**
     * Create a like for a model.
     */
    public static function createForModel(\Illuminate\Database\Eloquent\Model $model, int $userId): Like
    {
        return $model->likes()->create(['user_id' => $userId]);
    }

    /**
     * Get count of likes.
     */
    public static function countForModel(\Illuminate\Database\Eloquent\Model $model): int
    {
        return $model->likes()->count();
    }

    protected static function booted(): void
    {
        static::created(function (Like $like) {
            $like->likeable?->increment('likes_count');
        });

        static::deleted(function (Like $like) {
            $like->likeable?->decrement('likes_count');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}
