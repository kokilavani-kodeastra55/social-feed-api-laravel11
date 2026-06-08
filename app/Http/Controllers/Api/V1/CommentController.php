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
use App\Helpers\HTTPResponse;
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
     *
     * @param StoreCommentRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        try {
            $comment = $this->commentService->createComment($post, $request->validated(), $request->user()->id);
            $comment->load('user');

            return HTTPResponse::created(new CommentResource($comment), 'Comment posted successfully.');
        } catch (\Throwable $e) {
            Log::error('Error creating comment: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to post comment.');
        }
    }

    /**
     * Store a reply to an existing comment.
     *
     * @param StoreCommentRequest $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function reply(StoreCommentRequest $request, Comment $comment): JsonResponse
    {
        try {
            $reply = $this->commentService->createReply($comment, $request->validated(), $request->user()->id);
            $reply->load('user');

            return HTTPResponse::created(new CommentReplyResource($reply), 'Reply posted successfully.');
        } catch (\Throwable $e) {
            Log::error('Error creating reply comment: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to post reply.');
        }
    }

    /**
     * Update the specified comment.
     *
     * @param UpdateCommentRequest $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        try {
            Gate::authorize('update', $comment);

            $updatedComment = $this->commentService->updateComment($comment, $request->validated());
            $updatedComment->load('user');

            return HTTPResponse::ok(new CommentResource($updatedComment), 'Comment updated successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return HTTPResponse::forbidden($e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error updating comment: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to update comment.');
        }
    }

    /**
     * Delete the specified comment.
     *
     * @param Comment $comment
     * @return JsonResponse
     */
    public function destroy(Comment $comment): JsonResponse
    {
        try {
            Gate::authorize('delete', $comment);

            $this->commentService->deleteComment($comment);

            return HTTPResponse::ok(null, 'Comment deleted successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return HTTPResponse::forbidden($e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error deleting comment: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to delete comment.');
        }
    }

    /**
     * Update the specified comment reply.
     *
     * @param UpdateCommentRequest $request
     * @param CommentReply $reply
     * @return JsonResponse
     */
    public function updateReply(UpdateCommentRequest $request, CommentReply $reply): JsonResponse
    {
        try {
            Gate::authorize('update', $reply);

            $updatedReply = $this->commentService->updateReply($reply, $request->validated());
            $updatedReply->load('user');

            return HTTPResponse::ok(new CommentReplyResource($updatedReply), 'Reply updated successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return HTTPResponse::forbidden($e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error updating reply: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to update reply.');
        }
    }

    /**
     * Delete the specified comment reply.
     *
     * @param CommentReply $reply
     * @return JsonResponse
     */
    public function destroyReply(CommentReply $reply): JsonResponse
    {
        try {
            Gate::authorize('delete', $reply);

            $this->commentService->deleteReply($reply);

            return HTTPResponse::ok(null, 'Reply deleted successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return HTTPResponse::forbidden($e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error deleting reply: ' . $e->getMessage(), ['exception' => $e]);
            return HTTPResponse::internalServerError('Failed to delete reply.');
        }
    }
}
