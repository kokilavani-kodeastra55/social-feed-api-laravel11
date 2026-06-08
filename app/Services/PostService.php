<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PostService
{
    /**
     * Get all posts, sorted dynamically with caching.
     *
     * @param string $sortBy
     * @param string $sortOrder
     * @return Collection
     */
    public function getAllPosts(string $sortBy = 'sorting_order', string $sortOrder = 'asc'): Collection
    {
        try {
            $cacheEnabled = env('ENABLE_API_CACHE', true);

            if ($cacheEnabled) {
                $version = Cache::rememberForever('posts_version', fn() => 1);
                $cacheKey = "posts:all:v{$version}:{$sortBy}:{$sortOrder}";

                return Cache::remember($cacheKey, 3600, function () use ($sortBy, $sortOrder) {
                    return Post::getAllPosts($sortBy, $sortOrder);
                });
            }

            return Post::getAllPosts($sortBy, $sortOrder);
        } catch (\Throwable $e) {
            Log::error('PostService getAllPosts error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Create a post.
     *
     * @param array $data
     * @param int $userId
     * @return Post
     */
    public function createPost(array $data, int $userId): Post
    {
        try {
            $data['user_id'] = $userId;
            $post = Post::create($data);

            $this->clearCache();

            return $post;
        } catch (\Throwable $e) {
            Log::error('PostService createPost error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Update a post.
     *
     * @param Post $post
     * @param array $data
     * @return Post
     */
    public function updatePost(Post $post, array $data): Post
    {
        try {
            $post->update($data);

            $this->clearCache();

            return $post;
        } catch (\Throwable $e) {
            Log::error('PostService updatePost error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Delete a post.
     *
     * @param Post $post
     * @return bool
     */
    public function deletePost(Post $post): bool
    {
        try {
            $deleted = $post->delete();

            if ($deleted) {
                $this->clearCache();
            }

            return $deleted;
        } catch (\Throwable $e) {
            Log::error('PostService deletePost error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Invalidate posts and feeds caches.
     *
     * @return void
     */
    protected function clearCache(): void
    {
        try {
            Cache::rememberForever('posts_version', fn() => 1);
            Cache::increment('posts_version');

            Cache::rememberForever('feed_version', fn() => 1);
            Cache::increment('feed_version');
        } catch (\Throwable $e) {
            Log::error('PostService cache clearing error: ' . $e->getMessage(), ['exception' => $e]);
        }
    }
}
