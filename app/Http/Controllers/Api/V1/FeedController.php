<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FeedResource;
use App\Services\FeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedController extends Controller
{
    protected FeedService $feedService;

    public function __construct(FeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    /**
     * Display a listing of social feed posts.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $feed = $this->feedService->getFeed($request->user()->id);

            return api_success(FeedResource::collection($feed), 'Social feed retrieved successfully.');
        } catch (\Throwable $e) {
            Log::error('Error fetching social feed: ', ['exception' => $e]);
            return api_error('Failed to retrieve social feed.', null, 500);
        }
    }
}
