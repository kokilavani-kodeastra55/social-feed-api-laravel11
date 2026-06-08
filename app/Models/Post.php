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
        'sorting_order',
    ];

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            if ($post->sorting_order === null || $post->sorting_order === 0) {
                $post->sorting_order = \Illuminate\Support\Facades\DB::transaction(function () {
                    $max = \Illuminate\Support\Facades\DB::table('posts')->lockForUpdate()->max('sorting_order');
                    return ($max ?? 0) + 1;
                });
            }
        });

        static::created(function (Post $post) {
            $userIds = User::pluck('id');
            $feedData = [];
            foreach ($userIds as $userId) {
                $feedData[] = [
                    'user_id' => $userId,
                    'post_id' => $post->id,
                ];
            }
            if (! empty($feedData)) {
                Feed::insert($feedData);
            }
        });
    }

    /**
     * Get all posts, sorted dynamically.
     */
    public static function getAllPosts(string $sortBy = 'sorting_order', string $sortOrder = 'asc')
    {
        $allowedSortColumns = ['sorting_order', 'created_at', 'likes_count', 'id'];
        $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'sorting_order';
        $sortOrder = strtolower($sortOrder) === 'desc' ? 'desc' : 'asc';

        return self::with('user')
            ->orderBy($sortBy, $sortOrder)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Retrieve the personalized social feed for a user, sorted dynamically.
     */
    public static function getPersonalizedFeed(int $userId, string $sortBy = 'sorting_order', string $sortOrder = 'asc')
    {
        $allowedSortColumns = ['sorting_order', 'created_at', 'likes_count', 'id'];
        $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'sorting_order';
        $sortOrder = strtolower($sortOrder) === 'desc' ? 'desc' : 'asc';

        $query = self::query()
            ->join('feeds', function ($join) use ($userId) {
                $join->on('posts.id', '=', 'feeds.post_id')
                     ->where('feeds.user_id', '=', $userId);
            })
            ->select('posts.*')
            ->selectRaw('
                EXISTS (
                    SELECT 1 FROM likes 
                    WHERE likes.likeable_id = posts.id 
                      AND likes.likeable_type = ? 
                      AND likes.user_id = ?
                ) as is_liked_by_logged_user
            ', [(new self)->getMorphClass(), $userId])
            ->selectRaw('
                EXISTS (
                    SELECT 1 FROM comments 
                    WHERE comments.post_id = posts.id 
                      AND comments.user_id = ?
                ) as is_commented_by_logged_user
            ', [$userId])
            ->selectRaw('
                (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) +
                (SELECT COUNT(*) FROM comment_replies JOIN comments ON comment_replies.comment_id = comments.id WHERE comments.post_id = posts.id) as comments_count
            ')
            ->selectRaw('
                EXISTS (
                    SELECT 1 FROM posts AS p2
                    WHERE p2.user_id = posts.user_id
                      AND (
                          EXISTS (
                              SELECT 1 FROM likes 
                              WHERE likes.likeable_id = p2.id 
                                AND likes.likeable_type = \'post\' 
                                AND likes.user_id = ?
                          )
                          OR EXISTS (
                              SELECT 1 FROM comments 
                              WHERE comments.post_id = p2.id 
                                AND comments.user_id = ?
                          )
                      )
                ) as has_interacted_with_author
            ', [$userId, $userId])
            ->with([
                'user:id,name',
                'comments' => function ($q) {
                    $q->with(['user:id,name', 'replies.user:id,name']);
                }
            ]);

        // Prioritize interaction & likes, then sort by selected column
        $query->orderBy('has_interacted_with_author', 'desc')
              ->orderBy('posts.likes_count', 'desc');

        if ($sortBy === 'sorting_order') {
            $query->orderBy('posts.sorting_order', $sortOrder);
        } else {
            $query->orderBy('posts.' . $sortBy, $sortOrder);
        }

        return $query->orderBy('posts.id', 'desc')->cursorPaginate(10);
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
