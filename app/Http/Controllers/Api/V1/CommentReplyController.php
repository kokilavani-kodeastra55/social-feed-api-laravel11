<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCommentRequest;
use App\Http\Resources\Api\V1\CommentReplyResource;
use App\Models\CommentReply;
use App\Services\CommentService;
use App\Helpers\HTTPResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CommentReplyController extends Controller
{
    protected CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Update the specified comment reply.
     *
     * @param StoreCommentRequest $request
     * @param CommentReply $reply
     * @return JsonResponse
     */
    public function update(StoreCommentRequest $request, CommentReply $reply): JsonResponse
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
    public function destroy(CommentReply $reply): JsonResponse
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
