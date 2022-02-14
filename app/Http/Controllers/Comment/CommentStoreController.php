<?php

namespace App\Http\Controllers\Comment;

use App\Comment;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Comment\CommentPostRequest;

class CommentStoreController extends ApiController
{
    private $comment;

    public function __construct(Comment $comment)
    {
        $this->middleware('jwt:api');
        $this->comment = $comment;
    }




    /**
     * @OA\Post(
     *     path="/api/v1/comments",
     *     summary="Crea un comentario",
     *     tags={"Comments"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"comment", "group_user_id"},
     *                 @OA\Property(
     *                     property="comment",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="group_user_id",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una categoria",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en validaciones de negocio.",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Entidad no procesable.",
     *     ),
     *     security={ {"bearer_token": {}} },
     * )
     */
    function store(CommentPostRequest $request)
    {
        try {
            $this->comment = $this->comment->create($this->comment->setData($request));
            return $this->showOne($this->comment,200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),400);
        }
    }
}
