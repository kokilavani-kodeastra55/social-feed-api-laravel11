<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostRepository
{
    /**
     * Get all posts, eager loading their author user.
     */
    public function all(): Collection
    {
        return Post::query()->with('user')->latest()->get();
    }

    /**
     * Find a post by ID.
     */
    public function findById(int $id): ?Post
    {
        return Post::query()->find($id);
    }

    /**
     * Create a new post.
     */
    public function create(array $data): Post
    {
        return Post::query()->create($data);
    }

    /**
     * Update an existing post.
     */
    public function update(Post $post, array $data): Post
    {
        $post->update($data);
        return $post;
    }

    /**
     * Delete a post.
     */
    public function delete(Post $post): bool
    {
        return (bool) $post->delete();
    }

    /**
     * Retrieve the personalized social feed for a user.
     */
    public function getPersonalizedFeed(int $userId): mixed
    {
        $subQuery = Post::query()
            ->select('posts.*')
            // Priority 1: User interacted (liked or commented)
            ->selectRaw('
                (EXISTS (
                    SELECT 1 FROM likes 
                    WHERE likes.likeable_id = posts.id 
                      AND likes.likeable_type = ? 
                      AND likes.user_id = ?
                ) OR EXISTS (
                    SELECT 1 FROM comments 
                    WHERE comments.post_id = posts.id 
                      AND comments.user_id = ?
                )) as user_interacted
            ', [Post::class, $userId, $userId])
            // Priority 2: Popularity score = (likes * 3) + (comments * 2)
            ->selectRaw('
                ((SELECT COUNT(*) FROM likes WHERE likes.likeable_id = posts.id AND likes.likeable_type = ?) * 3 + 
                 (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) * 2) as popularity_score
            ', [Post::class])
            // Flags for response mapping
            ->selectRaw('
                EXISTS (
                    SELECT 1 FROM likes 
                    WHERE likes.likeable_id = posts.id 
                      AND likes.likeable_type = ? 
                      AND likes.user_id = ?
                ) as is_liked_by_logged_user
            ', [Post::class, $userId])
            ->selectRaw('
                EXISTS (
                    SELECT 1 FROM comments 
                    WHERE comments.post_id = posts.id 
                      AND comments.user_id = ?
                ) as is_commented_by_logged_user
            ', [$userId])
            ->selectRaw('
                (SELECT COUNT(*) FROM likes 
                 WHERE likes.likeable_id = posts.id 
                   AND likes.likeable_type = ?
                ) as likes_count
            ', [Post::class])
            ->selectRaw('
                (SELECT COUNT(*) FROM comments 
                 WHERE comments.post_id = posts.id
                ) as comments_count
            ');

        return Post::query()
            ->fromSub($subQuery, 'posts')
            ->with([
                'user:id,name',
                'comments' => function ($query) {
                    $query->whereNull('parent_id')
                        ->with(['user:id,name', 'replies.user:id,name'])
                        ->withCount(['likes', 'replies']);
                },
                'comments.replies' => function ($query) {
                    $query->withCount('likes');
                }
            ])
            ->orderBy('user_interacted', 'desc')
            ->orderBy('popularity_score', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate(10);
    }
}
