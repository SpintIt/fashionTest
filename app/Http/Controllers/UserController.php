<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return Response::deny(__('Всех не покажем, смотри только себя.'));
    }

    public function store(): Response
    {
        return Response::deny(__('Нельзя создать пользователей без регистрации.'));
    }

    public function show(User $user): UserResource
    {
        $this->authorize('show', $user);
        return new UserResource($user);
    }

    public function update(): Response
    {
        return Response::deny(__('Обновлять тоже нельзя.'));
    }

    public function destroy(): Response
    {
        return Response::deny(__('Самовыпиливайся в другом месте.'));
    }
}
