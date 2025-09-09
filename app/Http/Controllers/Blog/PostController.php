<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StorePostRequest;
use App\Http\Requests\Blog\UpdatePostRequest;
use App\Http\Resources\Blog\PostResource;
use App\Models\Blog\Post;
use App\Models\User;
use App\Services\Post\IPostService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 * name="Posts",
 * description="API Endpoints of Posts"
 * )
 * @OA\Schema(
 * schema="PostResource",
 * title="Post Resource",
 * description="Post resource model",
 * @OA\Property(
 * property="id",
 * type="integer",
 * format="int64",
 * description="ID of the post"
 * ),
 * @OA\Property(
 * property="body",
 * type="string",
 * description="The body of the post"
 * ),
 * @OA\Property(
 * property="status",
 * type="string",
 * description="The status of the post"
 * ),
 * @OA\Property(
 * property="created_at",
 * type="string",
 * format="date-time",
 * description="Creation timestamp"
 * ),
 * @OA\Property(
 * property="updated_at",
 * type="string",
 * format="date-time",
 * description="Last update timestamp"
 * )
 * )
 * @OA\Schema(
 * schema="StorePostRequest",
 * title="Store Post Request",
 * description="Request body for creating a post",
 * required={"body"},
 * @OA\Property(
 * property="body",
 * type="string",
 * description="The body of the post"
 * ),
 * @OA\Property(
 * property="status",
 * type="string",
 * description="The status of the post"
 * )
 * )
 * @OA\Schema(
 * schema="UpdatePostRequest",
 * title="Update Post Request",
 * description="Request body for updating a post",
 * @OA\Property(
 * property="body",
 * type="string",
 * description="The body of the post"
 * ),
 * @OA\Property(
 * property="status",
 * type="string",
 * description="The status of the post"
 * )
 * )
 */
class PostController extends Controller
{
    use AuthorizesRequests;

    private IPostService $postService;

    public function __construct(IPostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * @OA\Get(
     * path="/api/posts",
     * operationId="getPostsList",
     * tags={"Posts"},
     * summary="Получить список всех постов",
     * @OA\Response(
     * response=200,
     * description="Список постов успешно получен",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/PostResource")
     * )
     * )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        return PostResource::collection($this->postService->getAll());
    }

    /**
     * @OA\Post(
     * path="/api/posts",
     * operationId="createPost",
     * tags={"Posts"},
     * summary="Создать новый пост",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(ref="#/components/schemas/StorePostRequest")
     * ),
     * @OA\Response(
     * response=201,
     * description="Пост успешно создан",
     * @OA\JsonContent(ref="#/components/schemas/PostResource")
     * ),
     * @OA\Response(
     * response=401,
     * description="Не авторизован",
     * )
     * )
     */
    public function store(StorePostRequest $request): PostResource
    {
        $task = $this->postService->create($request->validated());
        return new PostResource($task);
    }

    /**
     * @OA\Get(
     * path="/api/posts/{id}",
     * operationId="getPostById",
     * tags={"Posts"},
     * summary="Получить пост по ID",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Пост успешно найден",
     * @OA\JsonContent(ref="#/components/schemas/PostResource")
     * ),
     * @OA\Response(
     * response=404,
     * description="Пост не найден"
     * )
     * )
     */
    public function show(Post $post): PostResource
    {
        return new PostResource($this->postService->show($post));
    }

    /**
     * @OA\Put(
     * path="/api/posts/{id}",
     * operationId="updatePost",
     * tags={"Posts"},
     * summary="Обновить существующий пост",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(ref="#/components/schemas/UpdatePostRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Пост успешно обновлен",
     * @OA\JsonContent(ref="#/components/schemas/PostResource")
     * ),
     * @OA\Response(
     * response=403,
     * description="Запрещено"
     * ),
     * @OA\Response(
     * response=404,
     * description="Пост не найден"
     * )
     * )
     */
    public function update(UpdatePostRequest $request, Post $post): PostResource
    {
        $this->authorize('update', $post);
        $updatedTask = $this->postService->update($post, $request->validated());
        return new PostResource($updatedTask);
    }

    /**
     * @OA\Delete(
     * path="/api/posts/{id}",
     * operationId="deletePost",
     * tags={"Posts"},
     * summary="Удалить пост",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="Пост успешно удален"
     * ),
     * @OA\Response(
     * response=403,
     * description="Запрещено"
     * ),
     * @OA\Response(
     * response=404,
     * description="Пост не найден"
     * )
     * )
     */
    public function destroy(Post $post): Response
    {
        $this->authorize('delete', $post);
        $this->postService->delete($post);
        return response()->noContent();
    }

    /**
     * @OA\Get(
     * path="/api/users/{id}/active-posts",
     * operationId="getActivePostsByUser",
     * tags={"Posts"},
     * summary="Получить список активных постов пользователя по его ID",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Список активных постов успешно получен",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/PostResource")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Пользователь не найден"
     * )
     * )
     */
    public function getActivePostsByUser(Request $request, User $user): AnonymousResourceCollection
    {
        $posts = $user->posts()->where('status', 'active')->get();
        return PostResource::collection($posts);
    }

    /**
     * @OA\Get(
     * path="/api/my-posts",
     * operationId="getMyPosts",
     * tags={"Posts"},
     * summary="Получить список постов текущего аутентифицированного пользователя",
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Список постов успешно получен",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/PostResource")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Не авторизован"
     * )
     * )
     */
    public function getMyPosts(Request $request): AnonymousResourceCollection
    {
        $posts = Auth::user()->posts()->get();
        return PostResource::collection($posts);
    }
}
