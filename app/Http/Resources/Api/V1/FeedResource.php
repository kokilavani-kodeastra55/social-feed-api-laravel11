<?php

namespace App\Http\Resources\Api\V1;

class FeedResource extends BaseApiResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->created_at?->toIso8601String(),
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null,
            'likes_count' => (int) ($this->likes_count ?? $this->likes()->count()),
            'comments_count' => (int) ($this->comments_count ?? $this->comments()->count()),
            'is_liked' => (bool) ($this->is_liked ?? $this->is_liked_by_logged_user ?? $this->likes()->where('user_id', auth()->id())->exists()),
            'is_liked_by_logged_user' => (bool) ($this->is_liked_by_logged_user ?? $this->likes()->where('user_id', auth()->id())->exists()),
            'is_commented_by_logged_user' => (bool) ($this->is_commented_by_logged_user ?? $this->comments()->where('user_id', auth()->id())->exists()),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
