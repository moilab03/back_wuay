<?php

namespace App\Http\Controllers\Comment;

use App\Comment;
use App\GroupUser;
use App\Http\Controllers\ApiController;
use App\Transformers\CommentTransformer;
use Illuminate\Http\Request;


class CommentIndexController extends ApiController
{
    private $comment;

    public function __construct(Comment $comment)
    {
        $this->middleware('administrator');
        $this->comment = $comment;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/comments?commerce={commerce_id}",
     *     summary="Obtiene los comentarios enviados por los usuarios, enviar sin commerce para administrador",
     *     tags={"Comments"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de comercios",
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
     * )
     */
    function index(Request $request)
    {
        try {
            if ($request->has('commerce')) {
                $groupUser = GroupUser::where('commerce_id', $request->commerce)->pluck('id');
                $comments = $this->comment->whereIn('group_user_id', $groupUser)->get();
            } else {
                $comments = $this->comment->all();
            }
            return $this->showAll($comments, CommentTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
