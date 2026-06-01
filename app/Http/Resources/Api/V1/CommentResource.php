<?php

namespace App\Http\Resources\Api\V1;

class CommentResource extends BaseApiResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'user' => $this->relationLoaded('user') && $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null,
            'user_details' => $this->relationLoaded('user') && $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null,
            'comment_user' => $this->relationLoaded('user') && $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null,
            'likes_count' => (int) ($this->likes_count ?? $this->likes()->count()),
            'reply_count' => (int) ($this->replies_count ?? $this->replies()->count()),
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
