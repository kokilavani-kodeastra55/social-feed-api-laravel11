<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCommentRequest;
use App\Http\Requests\Api\V1\UpdateCommentRequest;
use App\Http\Resources\Api\V1\CommentResource;
use App\Http\Resources\Api\V1\CommentReplyResource;
use App\Services\CommentService;
use App\Models\Post;
use App\Models\Comment;
use App\Models\CommentReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    protected CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Store a root comment on a post.
     */
    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        try {
            $comment = $this->commentService->createComment($post, $request->validated(), $request->user()->id);
            $comment->load('user');

            return api_success(new CommentResource($comment), 'Comment posted successfully.', 201);
        } catch (\Throwable $e) {
            Log::error('Error creating comment: ' . $e->getMessage());
            return api_error('Failed to post comment.', null, 500);
        }
    }

    /**
     * Store a reply to an existing comment.
     */
    public function reply(StoreCommentRequest $request, Comment $comment): JsonResponse
    {
        try {
            $reply = $this->commentService->createReply($comment, $request->validated(), $request->user()->id);
            $reply->load('user');

            return api_success(new CommentReplyResource($reply), 'Reply posted successfully.', 201);
        } catch (\Throwable $e) {
            Log::error('Error creating reply comment: ' . $e->getMessage());
            return api_error('Failed to post reply.', null, 500);
        }
    }

    /**
     * Update the specified comment.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        try {
            Gate::authorize('update', $comment);

            $updatedComment = $this->commentService->updateComment($comment, $request->validated());
            $updatedComment->load('user');

            return api_success(new CommentResource($updatedComment), 'Comment updated successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return api_error($e->getMessage(), null, 403);
        } catch (\Throwable $e) {
            Log::error('Error updating comment: ' . $e->getMessage());
            return api_error('Failed to update comment.', null, 500);
        }
    }

    /**
     * Delete the specified comment.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        try {
            Gate::authorize('delete', $comment);

            $this->commentService->deleteComment($comment);

            return api_success(null, 'Comment deleted successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return api_error($e->getMessage(), null, 403);
        } catch (\Throwable $e) {
            Log::error('Error deleting comment: ' . $e->getMessage());
            return api_error('Failed to delete comment.', null, 500);
        }
    }

    /**
     * Update the specified comment reply.
     */
    public function updateReply(UpdateCommentRequest $request, CommentReply $reply): JsonResponse
    {
        try {
            Gate::authorize('update', $reply);

            $updatedReply = $this->commentService->updateReply($reply, $request->validated());
            $updatedReply->load('user');

            return api_success(new CommentResource($updatedReply), 'Reply updated successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return api_error($e->getMessage(), null, 403);
        } catch (\Throwable $e) {
            Log::error('Error updating reply: ' . $e->getMessage());
            return api_error('Failed to update reply.', null, 500);
        }
    }

    /**
     * Delete the specified comment reply.
     */
    public function destroyReply(CommentReply $reply): JsonResponse
    {
        try {
            Gate::authorize('delete', $reply);

            $this->commentService->deleteReply($reply);

            return api_success(null, 'Reply deleted successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return api_error($e->getMessage(), null, 403);
        } catch (\Throwable $e) {
            Log::error('Error deleting reply: ' . $e->getMessage());
            return api_error('Failed to delete reply.', null, 500);
        }
    }
}
