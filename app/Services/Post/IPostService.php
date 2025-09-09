<?php

namespace App\Services\Post;

use App\Models\Blog\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

interface IPostService
{
    public function getAll(): Collection;
    public function create(array $data): Post;
    public function show(Post $post): Post;
    public function update(Post $post, array $data): Post;
    public function delete(Post $post): ?bool;
}
