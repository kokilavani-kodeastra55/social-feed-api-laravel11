<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePostRequest;
use App\Http\Requests\Api\V1\UpdatePostRequest;
use App\Http\Resources\Api\V1\PostResource;
use App\Services\PostService;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
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
     * Display a listing of posts.
     */
    public function index(): JsonResponse
    {
        try {
            $posts = $this->postService->getAllPosts();
            return api_success(PostResource::collection($posts), 'Posts retrieved successfully.');
        } catch (\Throwable $e) {
            Log::error('Error fetching posts: ' . $e->getMessage());
            return api_error('Failed to retrieve posts.', null, 500);
        }
    }

    /**
     * Store a newly created post.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $post = $this->postService->createPost($request->validated(), $request->user()->id);
            $post->load('user');
            return api_success(new PostResource($post), 'Post created successfully.', 201);
        } catch (\Throwable $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            return api_error('Failed to create post.', null, 500);
        }
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): JsonResponse
    {
        try {
            $post->load('user');
            return api_success(new PostResource($post), 'Post retrieved successfully.');
        } catch (\Throwable $e) {
            Log::error('Error retrieving post: ' . $e->getMessage());
            return api_error('Failed to retrieve post.', null, 500);
        }
    }

    /**
     * Update the specified post.
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        try {
            Gate::authorize('update', $post);

            $updatedPost = $this->postService->updatePost($post, $request->validated());
            $updatedPost->load('user');

            return api_success(new PostResource($updatedPost), 'Post updated successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return api_error($e->getMessage(), null, 403);
        } catch (\Throwable $e) {
            Log::error('Error updating post: ' . $e->getMessage());
            return api_error('Failed to update post.', null, 500);
        }
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        try {
            Gate::authorize('delete', $post);

            $this->postService->deletePost($post);

            return api_success(null, 'Post deleted successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return api_error($e->getMessage(), null, 403);
        } catch (\Throwable $e) {
            Log::error('Error deleting post: ' . $e->getMessage());
            return api_error('Failed to delete post.', null, 500);
        }
    }
}
