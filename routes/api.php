<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Blog\CommentController;
use App\Http\Controllers\Blog\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class);
    Route::apiResource('comments', CommentController::class);
    Route::apiResource('users', UserController::class);


    Route::get('/posts/{user}/active', [PostController::class, 'getActivePostsByUser']);
    Route::get('/posts/my/all', [PostController::class, 'getMyPosts']);


    Route::post('/comments/{post}/posts', [CommentController::class, 'storeToPost']);
    Route::post('/comments/{comment}/comments', [CommentController::class, 'storeToComment']);


    Route::get('/comments/{user}/users/active', [CommentController::class, 'getCommentsForActivePostsByUser']);
    Route::get('/comments/my/all', [CommentController::class, 'getMyComments']);
    Route::get('/comments/{post}/posts', [CommentController::class, 'getCommentsByPost']);
    Route::get('/comments/{comment}/replies', [CommentController::class, 'getRepliesToComment']);
});
