<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CommentReply extends Model
{
    use HasFactory;

    protected $table = 'comment_replies';

    protected $fillable = [
        'user_id',
        'comment_id',
        'comment',
        'likes_count',
    ];

    protected static function booted(): void
    {
        static::created(function (CommentReply $reply) {
            $reply->parentComment?->increment('replies_count');
        });

        static::deleted(function (CommentReply $reply) {
            $reply->parentComment?->decrement('replies_count');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentComment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
