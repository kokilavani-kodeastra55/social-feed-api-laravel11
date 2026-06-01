<?php

namespace App\Services;

use App\Repositories\PostRepository;

class FeedService
{
    protected PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Get social feed.
     */
    public function getFeed(int $userId): mixed
    {
        return $this->postRepository->getPersonalizedFeed($userId);
    }
}
