<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\Collection;

class PostService
{
    protected PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Get all posts with user author.
     */
    public function getAllPosts(): Collection
    {
        return $this->postRepository->all();
    }

    /**
     * Create a post.
     */
    public function createPost(array $data, int $userId): Post
    {
        $data['user_id'] = $userId;

        return $this->postRepository->create($data);
    }
    /**
     * Update a post.
     */
    public function updatePost(Post $post, array $data): Post
    {
        return $this->postRepository->update($post, $data);
    }

    /**
     * Delete a post.
     */
    public function deletePost(Post $post): bool
    {
        return $this->postRepository->delete($post);
    }
}
