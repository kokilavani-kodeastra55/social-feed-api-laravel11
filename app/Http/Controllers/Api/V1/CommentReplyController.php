<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCommentRequest;
use App\Http\Resources\Api\V1\CommentReplyResource;
use App\Models\CommentReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CommentReplyController extends Controller
{
    /**
     * Update the specified comment reply.
     */
    public function update(StoreCommentRequest $request, CommentReply $reply): JsonResponse
    {
        try {
            Gate::authorize('update', $reply);

            $reply->update($request->validated());
            $reply->load('user');

            return api_success(new CommentReplyResource($reply), 'Reply updated successfully.');
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
    public function destroy(CommentReply $reply): JsonResponse
    {
        try {
            Gate::authorize('delete', $reply);

            $reply->delete();

            return api_success(null, 'Reply deleted successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return api_error($e->getMessage(), null, 403);
        } catch (\Throwable $e) {
            Log::error('Error deleting reply: ' . $e->getMessage());
            return api_error('Failed to delete reply.', null, 500);
        }
    }
}
