<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Http\Requests\User\UserPhonePostRequest;
use App\Http\Requests\User\UserPostRequest;
use App\Rol;
use App\Services\AuthenticationService;
use App\User;

class UserStoreController extends ApiController
{
    protected $user;
    protected $authService;

    public function __construct(User $user, AuthenticationService $authService)
    {
        $this->middleware('administrator')->only('storeAdminCommerce');
        $this->user = $user;
        $this->authService = $authService;
    }


    /**
     * @OA\Post(
     *     path="/api/v1/users/commerce",
     *     summary="Crea un usuario administrador de restaurante",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email", "password", "name"},
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
    function storeAdminCommerce(UserPostRequest $request)
    {
        try {
            $this->user = $this->user->create(
                $this->user->setDataAdminCommerce($request->all())
            );
            return $this->showOne($this->user, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     summary="Crea un usuario, se debe enviar el JTW otorgado por firebase",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"id_token_string", "commerce_id", "name","terms_and_conditions"},
     *                 @OA\Property(
     *                     property="id_token_string",
     *                     type="string",
     *                 ),
     *       @OA\Property(
     *                     property="terms_and_conditions",
     *                     type="boolean",
     *                 ),
     *                 @OA\Property(
     *                     property="commerce_id",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
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
    function storeUser(UserPhonePostRequest $request)
    {
        try {
            $this->validateTerms($request->terms_and_conditions);
            $phone = $this->authService->getNumberPhone($request->id_token_string);
            $this->validatePhone($phone);
            $this->user = $this->user->create(
                $this->user->setDataUser($request, $phone)
            );
            return $this->showOne($this->user, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    function validateTerms($terms)
    {
        if(!$terms)
            throw new \Exception("Debe aceptar terminos y condiciones");
    }


    /**
     * @param $phone
     * @throws \Exception
     */
    function validatePhone($phone)
    {
        $count = $this->user
            ->where('phone', $phone)
            ->where('rol_id', Rol::byRol(Rol::USER)->value('id'))
            ->count();

        if ($count > 0) {
            throw new \Exception("Ya existe una cuenta con este número de celular, inicia sesión");
        }
    }
}
