<?php

namespace App\Services\Post;

use App\Models\Blog\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PostService implements IPostService
{

    public function getAll(): Collection
    {
        return Post::all();
    }

    public function create(array $data): Post
    {
        return Auth::user()->posts()->create($data);
    }

    public function show(Post $post): Post
    {
        return $post;
    }

    public function update(Post $post, array $data): Post
    {
        $post->update($data);
        return $post;
    }

    public function delete(Post $post): ?bool
    {
        return $post->delete();
    }
}
