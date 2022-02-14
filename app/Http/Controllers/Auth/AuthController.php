<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\AuthPhonePostRequest;
use App\Rol;
use App\Services\AuthenticationService;
use App\Status;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\AuthPostCheckRequest;
use App\Http\Requests\Auth\AuthAdministratorRequest;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends ApiController
{
    protected $rol;
    protected $status;
    protected $authService;
    protected $user;

    public function __construct(Rol $rol, Status $status, AuthenticationService $authService, User $user)
    {
        $this->middleware('jwt:api')
            ->only(['checkAuth']);
        $this->rol = $rol;
        $this->authService = $authService;
        $this->status = $status;
        $this->user = $user;
    }


    /**
     * @OA\Post(
     *     path="/api/v1/auth/admin",
     *     summary="Login de los administradores de tender, y administradores de comercio",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email", "password", "rol"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="rol",
     *                     type="string",
     *                     enum={"Administrador", "Administrador comercio"}
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
     * )
     */
    public function login(AuthAdministratorRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $token = $this->validateCredentials($credentials);
            $this->validateState();
            $this->validateRol($request->rol);
            return $this->responseToken($token);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    protected function responseToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 36000 * 60 * 60
        ]);
    }

    /**
     * @param $credentials
     * @return bool
     * @throws \Exception
     */
    protected function validateCredentials($credentials)
    {
        $token = auth()->attempt($credentials);
        if ($token)
            return $token;
        throw new \Exception("Datos incorrectos, verifique su email y contraseña");
    }

    /**
     * @throws \Exception
     */
    protected function validateState()
    {
        if (auth()->user()->status_id !== $this->status->byStatus(Status::ENABLED)->value('id')) {
            auth()->logout();
            throw new \Exception("Este usuario ha sido inhabilitado por los administradores");
        }
    }

    /**
     * @param $rol
     * @throws \Exception
     */
    protected function validateRol($rol)
    {
        if (auth()->user()->rol_id !== $this->rol->byRol($rol)->value('id')) {
            auth()->logout();
            throw new \Exception("No tiene permisos para ingresar por este medio");
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/auth/check",
     *     summary="Comprueba el token de verificación",
     *     tags={"Authentication"},
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
    public function checkAuth(AuthPostCheckRequest $request)
    {
        try {
            $this->validateState();
            if (auth()->check() &&
                auth()->user()->rol_id === $this->rol->byRol($request->rol)->value('id'))
                return $this->showMessage('Token vigente', 200);
            return $this->errorResponse('Token invalido, inicie sesión', 400);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     summary="Trae la información del usuario pasando el JWT",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un usuario",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="path",
     *         description="incluir datos relacionados con el usuario, si es usuario puede enviar ejemplo: url?include=commerce.",
     *         @OA\Schema(
     *             type="string",
     *             format="string"
     *         )
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
    public function me()
    {
        return $this->showOne(auth()->user(), 200);
    }


    /**
     * @OA\Post(
     *     path="/api/v1/auth/user",
     *     summary="Login de usuarios mediante validación de token de firebase (JWT sacado de la autenticación de telefono de firebase)",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"commerce_id", "id_token_string"},
     *                 @OA\Property(
     *                     property="id_token_string",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="commerce_id",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un token de autenticación mediante token de autenticación de firebase.",
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
    function authPhone(AuthPhonePostRequest $request)
    {
        try {
            $phone = $this->authService->getNumberPhone($request->id_token_string);
            $this->validatePhone($phone);
            $token = JWTAuth::fromUser($this->user);
            $this->validateRole();
            $this->validateStatus();
            $this->user->current_commerce_id = $request->commerce_id;
            $this->user->save();
            return $this->responseToken($token);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    /**
     * @throws \Exception
     */
    protected function validateStatus()
    {
        if ((int)$this->user->status_id !== $this->status->byStatus(Status::ENABLED)->value('id')) {
            throw new \Exception("Este usuario ha sido inhabilitado por los administradores");
        }
    }

    /**
     * @throws \Exception
     */
    protected function validateRole()
    {
        if ((int) $this->user->rol_id !== Rol::byRol(Rol::USER)->value('id')) {
            auth()->logout();
            throw new \Exception("No tiene permisos de usuario");
        }
    }

    /**
     * @param $phone
     * @throws \Exception
     */
    function validatePhone($phone)
    {
        $this->user = $this->user
            ->where('phone',$phone)
            ->where('rol_id', Rol::byRol(Rol::USER)->value('id'))
            ->first();

        if (!$this->user) {
            throw new \Exception("No existe una cuenta creada a este número celular");
        }
    }
}
