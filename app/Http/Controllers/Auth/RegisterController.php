<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Contracts\Support\Renderable;

/**
 * @OA\Schema(
 * schema="RegisterRequest",
 * title="Register Request",
 * description="Request body for user registration",
 * required={"name", "email", "password", "password_confirmation"},
 * @OA\Property(
 * property="name",
 * type="string",
 * description="User's name"
 * ),
 * @OA\Property(
 * property="email",
 * type="string",
 * format="email",
 * description="User's email"
 * ),
 * @OA\Property(
 * property="password",
 * type="string",
 * format="password",
 * description="User's password"
 * ),
 * @OA\Property(
 * property="password_confirmation",
 * type="string",
 * format="password",
 * description="Password confirmation"
 * )
 * )
 */
class RegisterController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/register",
     * operationId="registerUser",
     * tags={"Auth"},
     * summary="Регистрация нового пользователя",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Пользователь успешно зарегистрирован",
     * @OA\JsonContent(
     * @OA\Property(property="access_token", type="string", description="JWT access token"),
     * @OA\Property(property="token_type", type="string", example="Bearer")
     * )
     * )
     * )
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $user = User::query()->create($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
