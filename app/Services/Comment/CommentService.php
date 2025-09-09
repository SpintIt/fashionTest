<?php

namespace App\Services\Comment;

use App\Models\Blog\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class CommentService implements ICommentService
{

    public function getAll(): Collection
    {
        return Comment::all();
    }

    public function create(array $data): Comment
    {
        Auth::user()->posts()->create($data);
        return Comment::query()->create($data);
    }

    public function show(Comment $comment): Comment
    {
        return $comment;
    }

    public function update(Comment $comment, array $data): Comment
    {
        $comment->update($data);
        return $comment;
    }

    public function delete(Comment $comment): ?bool
    {
        return $comment->delete();
    }
}
