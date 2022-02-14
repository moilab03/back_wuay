<?php

namespace App\Http\Controllers\GroupUser;

use App\GroupUser;
use App\Http\Controllers\ApiController;
use App\Transformers\GroupUserTransformer;


class GroupUserIndexController extends ApiController
{
    protected $groupUser;

    public function __construct(GroupUser $groupUser)
    {
        $this->middleware('jwt:api');
        $this->groupUser = $groupUser;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/groupUsers",
     *     summary="Trae los grupos que se encuentran dentro del comercio",
     *     tags={"Group user"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de grupos",
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
    function index()
    {
        try {
            $this->groupUser = $this->groupUser->byCommerce(auth()->user()->current_commerce_id)->get();
            return $this->showAll($this->groupUser, GroupUserTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
