<?php

namespace App\Http\Controllers\GroupUser;

use App\GroupUser;
use App\Http\Controllers\ApiController;


class GroupUserShowController extends ApiController
{
    protected $groupUser;

    public function __construct(GroupUser $groupUser)
    {
        $this->middleware('jwt:api');
        $this->groupUser = $groupUser;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/user/groupUsers",
     *     summary="Trae el grupo de usuario del usuario",
     *     tags={"Group user"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un grupo de usuario",
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
    function show()
    {
        try {
            $this->groupUser = GroupUser::find(auth()->user()->principal_group_user_id);
            return $this->showOne($this->groupUser, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

}
