<?php

namespace App\Providers;

use App\Services\Api\V1\CommentService;
use App\Services\Api\V1\Contracts\CommentServiceInterface;
use App\Services\Api\V1\Contracts\FeedServiceInterface;
use App\Services\Api\V1\Contracts\LikeServiceInterface;
use App\Services\Api\V1\Contracts\PostServiceInterface;
use App\Services\Api\V1\FeedService;
use App\Services\Api\V1\LikeService;
use App\Services\Api\V1\PostService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FeedServiceInterface::class, FeedService::class);
        $this->app->bind(PostServiceInterface::class, PostService::class);
        $this->app->bind(CommentServiceInterface::class, CommentService::class);
        $this->app->bind(LikeServiceInterface::class, LikeService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
