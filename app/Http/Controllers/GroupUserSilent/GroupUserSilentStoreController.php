<?php

namespace App\Http\Controllers\GroupUserSilent;

use App\GroupUser;
use App\GroupUserRoom;
use App\GroupUserSilent;
use App\Http\Controllers\ApiController;
use App\Http\Requests\GroupUserSilent\GroupUserSilentPostRequest;
use Illuminate\Support\Facades\DB;

class GroupUserSilentStoreController extends ApiController
{
    protected $groupUserSilent;

    function __construct(GroupUserSilent $groupUserSilent)
    {
        $this->middleware('jwt:api');
        $this->groupUserSilent = $groupUserSilent;
    }


    /**
     * @OA\Post(
     *     path="/api/v1/groupUserSilents",
     *     summary="Silencia un grupo",
     *     tags={"Group user"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"group_user_id", "group_user_target_id"},
     *                 @OA\Property(
     *                     property="group_user_id",
     *                     type="string",
     *                 ),
     *      @OA\Property(
     *                     property="group_user_target_id",
     *                     type="string",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una grupo de usuario",
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

    function store(GroupUserSilentPostRequest $postRequest)
    {
        DB::beginTransaction();
        try {
            $this->validateIfExists($postRequest->group_user_id, $postRequest->group_user_target_id);
            $this->groupUserSilent = $this->groupUserSilent->create(
                $this->groupUserSilent->setData($postRequest)
            );
            DB::commit();
            return $this->showOne(GroupUser::find($this->groupUserSilent->group_user_id), 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    function validateIfExists($id, $target)
    {
        $count = $this->groupUserSilent
            ->byGroupUser($id)
            ->byGroupTarget($target)
            ->count();
        if ($count > 0)
            throw new \Exception('Ya se ha silenciado este grupo');
    }
}
