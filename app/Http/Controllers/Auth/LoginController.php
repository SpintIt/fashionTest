<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use \App\Http\Requests\Auth\LoginRequest;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Schema(
 * schema="LoginRequest",
 * title="Login Request",
 * description="Request body for user login",
 * required={"email", "password"},
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
 * )
 * )
 */
class LoginController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/login",
     * operationId="loginUser",
     * tags={"Auth"},
     * summary="Аутентификация пользователя",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Вход выполнен успешно",
     * @OA\JsonContent(
     * @OA\Property(property="access_token", type="string", description="JWT access token"),
     * @OA\Property(property="token_type", type="string", example="Bearer")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Неверные учетные данные"
     * )
     * )
     */
    public function store(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated()))
        {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed')
            ]);
        }

        $user = $request->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
