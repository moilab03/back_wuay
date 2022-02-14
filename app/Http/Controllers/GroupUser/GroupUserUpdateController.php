<?php

namespace App\Http\Controllers\GroupUser;

use App\GroupUser;
use App\Http\Controllers\ApiController;
use App\Http\Requests\GroupUser\GroupUserPutRequest;
use App\Status;
use Illuminate\Support\Facades\DB;

class GroupUserUpdateController extends ApiController
{
    protected $user;
    protected $groupUser;

    public function __construct(GroupUser $groupUser)
    {
        $this->middleware('jwt:api');
        $this->groupUser = $groupUser;
    }


    /**
     * @OA\Post(
     *     path="/api/v1/groupUsers",
     *     summary="Creación de un grupo de usuario",
     *     description="Tipos de usuarios accecibles en este endpoint: usuarios",
     *     tags={"Group user"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="photos",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         format="binary",
     *                     ),
     *                 ),
     *             )
     *         )
     *     ),
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
    function update(GroupUserPutRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->getGroupUser();
            $this->getUser();
            $this->groupUser->name = $request->group_name;
            $this->user->name = $request->user_name;
            $this->user->save();
            $this->groupUser->save();
            if ($request->has('photos'))
                $this->managePhotos($request->photos);
            DB::commit();
            return $this->showOne($this->groupUser, 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    function managePhotos($photos)
    {
        try {
            $this->groupUser->resources()->delete();
            $this->groupUser->savePhotosResource($photos);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }


    function getUser()
    {
        try {
            $this->user = auth()->user();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    function getGroupUser()
    {
        try {
            $this->groupUser = GroupUser::find(auth()->user()->principal_group_user_id);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
