<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FeedService
{
    /**
     * Get social feed with dynamic sorting and caching.
     *
     * @param int $userId
     * @param string $sortBy
     * @param string $sortOrder
     * @return mixed
     */
    public function getFeed(int $userId, string $sortBy = 'sorting_order', string $sortOrder = 'asc'): mixed
    {
        try {
            $cacheEnabled = env('ENABLE_API_CACHE', true);

            if ($cacheEnabled) {
                $version = Cache::rememberForever('feed_version', fn() => 1);
                $cursor = request()->get('cursor', 'default');
                $cacheKey = "user:{$userId}:feed:v{$version}:{$sortBy}:{$sortOrder}:{$cursor}";

                return Cache::remember($cacheKey, 3600, function () use ($userId, $sortBy, $sortOrder) {
                    return Post::getPersonalizedFeed($userId, $sortBy, $sortOrder);
                });
            }

            return Post::getPersonalizedFeed($userId, $sortBy, $sortOrder);
        } catch (\Throwable $e) {
            Log::error('FeedService getFeed error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }
}
