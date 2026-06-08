<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FeedResource;
use App\Services\FeedService;
use App\Helpers\HTTPResponse;
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
     * Display a listing of social feed posts with dynamic sorting.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $sortBy = $request->query('sort_by', 'sorting_order');
            $sortOrder = $request->query('sort_order', 'asc');

            $feed = $this->feedService->getFeed($request->user()->id, $sortBy, $sortOrder);

            return HTTPResponse::ok(FeedResource::collection($feed), 'Social feed retrieved successfully.');
        } catch (\Throwable $e) {
            Log::error('Error fetching social feed: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to retrieve social feed.');
        }
    }
}
