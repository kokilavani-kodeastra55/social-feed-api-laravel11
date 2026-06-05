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
        return Post::with('user')->latest()->get();
    }

    /**
     * Find a post by ID.
     */
    public function findById(int $id): ?Post
    {
        return Post::find($id);
    }

    /**
     * Create a new post.
     */
    public function create(array $data): Post
    {
        return Post::create($data);
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
        return $post->delete();
    }

    /**
     * Retrieve the personalized social feed for a user.
     */
    public function getPersonalizedFeed(int $userId): mixed
    {
        return Post::query()
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
            ', [(new Post)->getMorphClass(), $userId])
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
                'comments' => function ($query) {
                    $query->with(['user:id,name', 'replies.user:id,name']);
                }
            ])
            ->orderBy('has_interacted_with_author', 'desc')
            ->orderBy('posts.likes_count', 'desc')
            ->orderBy('posts.created_at', 'desc')
            ->orderBy('posts.id', 'desc')
            ->cursorPaginate(10);
    }
}
