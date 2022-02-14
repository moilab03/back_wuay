<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Http\Controllers\ApiController;
use App\Http\Requests\User\UserPutRequest;

class UserUpdateController extends ApiController
{

    public function __construct()
    {
        $this->middleware('administrator');
    }



    /**
     * @OA\Put(
     *     path="/api/v1/users/commerce/{user}",
     *     summary="Actualiza un usuario administrador de restaurante",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email", "password", "name", "_method"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="_method",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un token de autenticación mediante email.",
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
    function updateCommerce(UserPutRequest $request, User $user)
    {
        try {
            $user = $user->setDataUpdateCommerce($request);
            $user->save();
            return $this->showOne($user, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/status/{user}",
     *     summary="Cambia el estado de un usuario administrador del comercio",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un usuario",
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
    function changeState(User $user)
    {
        try {
            $user = $user->changeStatus();
            $user->save();
            return $this->showOne($user, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
