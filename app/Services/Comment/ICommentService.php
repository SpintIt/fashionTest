<?php

namespace App\Services\Comment;

use App\Models\Blog\Comment;
use Illuminate\Database\Eloquent\Collection;

interface ICommentService
{
    public function getAll(): Collection;
    public function create(array $data): Comment;
    public function show(Comment $comment): Comment;
    public function update(Comment $comment, array $data): Comment;
    public function delete(Comment $comment): ?bool;
}
