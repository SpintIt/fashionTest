<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StoreCommentRequest;
use App\Http\Requests\Blog\UpdateCommentRequest;
use App\Http\Resources\Blog\CommentResource;
use App\Models\Blog\Comment;
use App\Models\Blog\Post;
use App\Models\User;
use App\Services\Comment\ICommentService;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 * name="Comments",
 * description="API Endpoints of Comments"
 * )
 * @OA\Schema(
 * schema="CommentResource",
 * title="Comment Resource",
 * description="Comment resource model",
 * @OA\Property(
 * property="id",
 * type="integer",
 * format="int64",
 * description="ID of the comment"
 * ),
 * @OA\Property(
 * property="body",
 * type="string",
 * description="The body of the comment"
 * ),
 * @OA\Property(
 * property="created_at",
 * type="string",
 * format="date-time",
 * description="Creation timestamp"
 * )
 * )
 * @OA\Schema(
 * schema="StoreCommentRequest",
 * title="Store Comment Request",
 * description="Request body for creating a comment",
 * required={"body"},
 * @OA\Property(
 * property="body",
 * type="string",
 * description="The body of the comment"
 * )
 * )
 * @OA\Schema(
 * schema="UpdateCommentRequest",
 * title="Update Comment Request",
 * description="Request body for updating a comment",
 * @OA\Property(
 * property="body",
 * type="string",
 * description="The body of the comment"
 * )
 * )
 */
class CommentController extends Controller
{
    use AuthorizesRequests;

    private ICommentService $commentService;

    public function __construct(ICommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * @OA\Get(
     * path="/api/comments",
     * operationId="getCommentsList",
     * tags={"Comments"},
     * summary="Получить список всех комментариев",
     * @OA\Response(
     * response=200,
     * description="Список комментариев успешно получен",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/CommentResource")
     * )
     * )
     * )
     */
    public function index()
    {
        return CommentResource::collection($this->commentService->getAll());
    }

    /**
     * @OA\Post(
     * path="/api/comments",
     * operationId="storeComment",
     * tags={"Comments"},
     * summary="Создать комментарий (запрещено)",
     * @OA\Response(
     * response=403,
     * description="Запрещено"
     * )
     * )
     */
    public function store(StoreCommentRequest $request): Response
    {
        return Response::deny(__('Нельзя создать комментарий без указания поста.'));
    }

    /**
     * @OA\Get(
     * path="/api/comments/{comment}",
     * operationId="getCommentById",
     * tags={"Comments"},
     * summary="Получить комментарий по ID",
     * @OA\Parameter(
     * name="comment",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Комментарий успешно найден",
     * @OA\JsonContent(ref="#/components/schemas/CommentResource")
     * ),
     * @OA\Response(
     * response=404,
     * description="Комментарий не найден"
     * )
     * )
     */
    public function show(Comment $comment): CommentResource
    {
        return new CommentResource($this->commentService->show($comment));
    }

    /**
     * @OA\Put(
     * path="/api/comments/{comment}",
     * operationId="updateComment",
     * tags={"Comments"},
     * summary="Обновить существующий комментарий",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="comment",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(ref="#/components/schemas/UpdateCommentRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Комментарий успешно обновлен",
     * @OA\JsonContent(ref="#/components/schemas/CommentResource")
     * ),
     * @OA\Response(
     * response=403,
     * description="Запрещено"
     * ),
     * @OA\Response(
     * response=404,
     * description="Комментарий не найден"
     * )
     * )
     */
    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
        $this->authorize('update', $comment);
        $updatedTask = $this->commentService->update($comment, $request->validated());
        return new CommentResource($updatedTask);
    }

    /**
     * @OA\Delete(
     * path="/api/comments/{comment}",
     * operationId="deleteComment",
     * tags={"Comments"},
     * summary="Удалить комментарий",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="comment",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="Комментарий успешно удален"
     * ),
     * @OA\Response(
     * response=403,
     * description="Запрещено"
     * ),
     * @OA\Response(
     * response=404,
     * description="Комментарий не найден"
     * )
     * )
     */
    public function destroy(Comment $comment): \Illuminate\Http\Response
    {
        $this->authorize('delete', $comment);
        $this->commentService->delete($comment);
        return response()->noContent();
    }


    /**
     * @OA\Post(
     * path="/api/comments/{post}/posts",
     * operationId="storeCommentToPost",
     * tags={"Comments"},
     * summary="Добавить комментарий к посту",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="post",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(ref="#/components/schemas/StoreCommentRequest")
     * ),
     * @OA\Response(
     * response=201,
     * description="Комментарий успешно добавлен",
     * @OA\JsonContent(ref="#/components/schemas/CommentResource")
     * )
     * )
     */
    public function storeToPost(StoreCommentRequest $request, Post $post): CommentResource
    {
        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);
        return new CommentResource($comment);
    }

    /**
     * @OA\Post(
     * path="/api/comments/{comment}/comments",
     * operationId="storeCommentToComment",
     * tags={"Comments"},
     * summary="Ответить на комментарий",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="comment",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(ref="#/components/schemas/StoreCommentRequest")
     * ),
     * @OA\Response(
     * response=201,
     * description="Ответ успешно добавлен",
     * @OA\JsonContent(ref="#/components/schemas/CommentResource")
     * )
     * )
     */
    public function storeToComment(StoreCommentRequest $request, Comment $comment): CommentResource
    {
        info('info', [$comment->id]);
        $commentNew = $comment->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);
        return new CommentResource($commentNew);
    }

    /**
     * @OA\Get(
     * path="/api/comments/{user}/users/active",
     * operationId="getCommentsForActivePostsByUser",
     * tags={"Comments"},
     * summary="Получить комментарии к активным постам пользователя",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="user",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Список комментариев успешно получен",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/CommentResource")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Пользователь не найден"
     * )
     * )
     */
    public function getCommentsForActivePostsByUser(User $user): AnonymousResourceCollection
    {
        $comments = $user->getCommentsForActivePosts()->latest()->get();
        return CommentResource::collection($comments);
    }

    /**
     * @OA\Get(
     * path="/api/comments/my/all",
     * operationId="getMyComments",
     * tags={"Comments"},
     * summary="Получить все комментарии текущего аутентифицированного пользователя",
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Список комментариев успешно получен",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/CommentResource")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Не авторизован"
     * )
     * )
     */
    public function getMyComments(): AnonymousResourceCollection
    {
        $comments = Auth::user()->comments()->latest()->get();
        return CommentResource::collection($comments);
    }

    /**
     * @OA\Get(
     * path="/api/comments/{post}/posts",
     * operationId="getCommentsByPost",
     * tags={"Comments"},
     * summary="Получить комментарии для поста",
     * @OA\Parameter(
     * name="post",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Список комментариев успешно получен",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/CommentResource")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Пост не найден"
     * )
     * )
     */
    public function getCommentsByPost(Post $post): AnonymousResourceCollection
    {
        $comments = $post->comments()->latest()->get();
        return CommentResource::collection($comments);
    }

    /**
     * @OA\Get(
     * path="/api/comments/{comment}/replies",
     * operationId="getRepliesToComment",
     * tags={"Comments"},
     * summary="Получить ответы на комментарий",
     * @OA\Parameter(
     * name="comment",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Список ответов успешно получен",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/CommentResource")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Комментарий не найден"
     * )
     * )
     */
    public function getRepliesToComment(Comment $comment): AnonymousResourceCollection
    {
        $replies = $comment->comments()->latest()->get();
        return CommentResource::collection($replies);
    }
}
