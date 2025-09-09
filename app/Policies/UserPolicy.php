<?php

namespace App\Policies;

use App\Models\Blog\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Request;

class UserPolicy
{
    use HandlesAuthorization;

    public function show(User $user, User $userRequest): bool
    {
        return $user->id === $userRequest->id;
    }
}
