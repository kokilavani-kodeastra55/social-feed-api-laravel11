<?php

namespace App\Models;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'likes_count',
    ];

    protected static function booted(): void
    {
        static::created(function (Post $post) {
            $userIds = User::pluck('id');
            $feedData = [];
            foreach ($userIds as $userId) {
                $feedData[] = [
                    'user_id' => $userId,
                    'post_id' => $post->id,
                    'created_at' => $post->created_at ?? now(),
                    'updated_at' => $post->updated_at ?? now(),
                ];
            }
            if (! empty($feedData)) {
                Feed::insert($feedData);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feeds(): HasMany
    {
        return $this->hasMany(Feed::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
