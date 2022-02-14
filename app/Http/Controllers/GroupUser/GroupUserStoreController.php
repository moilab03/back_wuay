<?php

namespace App\Http\Controllers\GroupUser;

use App\GroupUser;
use App\Http\Controllers\ApiController;
use App\Http\Requests\GroupUser\GroupUserPostRequest;
use App\Status;
use Illuminate\Support\Facades\DB;


class GroupUserStoreController extends ApiController
{
    protected $groupUser;

    function __construct(GroupUser $groupUser)
    {
        $this->middleware('jwt:api');
        $this->groupUser = $groupUser;
    }

    /**
     * @OA\Put(
     *     path="/api/v1/groupUsers",
     *     summary="Actualización de un grupo de usuario",
     *     description="Tipos de usuarios accecibles en este endpoint: usuarios",
     *     tags={"Group user"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"group_name","photos","user_name"},
     *                 @OA\Property(
     *                     property="group_name",
     *                     type="string",
     *                 ),
     *      @OA\Property(
     *                     property="user_name",
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
    function store(GroupUserPostRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->verifyIfHasGroupInCommerce();
            $this->groupUser = $this->groupUser->create(
                $this->groupUser->setData($request)
            );
            if ($request->has('photos'))
                $this->groupUser->savePhotosResource($request->photos);
            $this->addPrincipalGroup();
            DB::commit();
            return $this->showOne($this->groupUser, 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    protected function addPrincipalGroup()
    {
        try {

            $user = auth()->user();
            if ($user->principal_group_user_id === null) {
                $user->principal_group_user_id = $this->groupUser->id;
                $user->save();
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    protected function verifyIfHasGroupInCommerce()
    {
        $count = $this->groupUser
            ->byUser(auth()->user()->id)
            ->byCommerce(auth()->user()->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->count();
        if ($count > 0)
            throw new \Exception('Ya tiene creado un grupo creado en este comercio');
    }
}
