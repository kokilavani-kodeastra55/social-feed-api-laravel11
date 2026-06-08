<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePostRequest;
use App\Http\Requests\Api\V1\UpdatePostRequest;
use App\Http\Resources\Api\V1\PostResource;
use App\Services\PostService;
use App\Models\Post;
use App\Helpers\HTTPResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of posts with dynamic sorting.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $sortBy = $request->query('sort_by', 'sorting_order');
            $sortOrder = $request->query('sort_order', 'asc');

            $posts = $this->postService->getAllPosts($sortBy, $sortOrder);
            return HTTPResponse::ok(PostResource::collection($posts), 'Posts retrieved successfully.');
        } catch (\Throwable $e) {
            Log::error('Error fetching posts: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to retrieve posts.');
        }
    }

    /**
     * Store a newly created post.
     *
     * @param StorePostRequest $request
     * @return JsonResponse
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $post = $this->postService->createPost($request->validated(), $request->user()->id);
            $post->load('user');
            return HTTPResponse::created(new PostResource($post), 'Post created successfully.');
        } catch (\Throwable $e) {
            Log::error('Error creating post: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to create post.');
        }   
    }

    /**
     * Display the specified post.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        try {
            $post->load('user');
            return HTTPResponse::ok(new PostResource($post), 'Post retrieved successfully.');
        } catch (\Throwable $e) {
            Log::error('Error retrieving post: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to retrieve post.');
        }
    }

    /**
     * Update the specified post.
     *
     * @param UpdatePostRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        try {
            Gate::authorize('update', $post);

            $updatedPost = $this->postService->updatePost($post, $request->validated());
            $updatedPost->load('user');

            return HTTPResponse::ok(new PostResource($updatedPost), 'Post updated successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return HTTPResponse::forbidden($e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error updating post: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to update post.');
        }
    }

    /**
     * Remove the specified post from storage.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Post $post): JsonResponse
    {
        try {
            Gate::authorize('delete', $post);

            $this->postService->deletePost($post);

            return HTTPResponse::ok(null, 'Post deleted successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return HTTPResponse::forbidden($e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error deleting post: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to delete post.');
        }
    }
}
