<?php

namespace App\Http\Controllers\UserDevice;

use App\UserDevice;
use App\Http\Controllers\ApiController;
use App\Http\Requests\UserDevice\UserDeviceStoreRequest;

class UserDeviceController extends ApiController
{
    protected $userDevice;
    protected $action;
    protected $user;
    protected $device;

    public function __construct(UserDevice $userDevice)
    {
        $this->middleware('jwt:api');
        $this->userDevice = $userDevice;
        $this->action = '';
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register/user_devices",
     *     summary="Registra/actualiza un dispositivo asociado al usuario logueado",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"token", "os"},
     *                 @OA\Property(
     *                     property="token",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="os",
     *                     type="string",
     *                     enum={"ios", "web", "android"}
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Muestra mensaje: Se ha guardado con exito.",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en validaciones de negocio.",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuario no autorizado.",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Entidad no procesable.",
     *     ),
     *     security={ {"bearer_token": {}} },
     * )
     */
    public function store(UserDeviceStoreRequest $request)
    {
        try {
            $this->user = auth()->user();
            $this->verifyIfExists($request->os);
            $this->handleAction($request);

            return $this->showMessage('Se ha guardado con exito', 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @param $os
     */
    protected function verifyIfExists($os)
    {
        $this->device = $this->user->devices()->os($os);

        if ($this->device->count() > 0)
            $this->action = 'update';
        else
            $this->action = 'create';
    }

    /**
     * @param $attributes
     */
    protected function handleAction($attributes)
    {
        switch ($this->action) {
            case 'update':
                $this->device = $this->device->first();
                $this->device = $this->device->setDataUpdate($attributes);
                $this->device->save();
                break;
            case 'create':
                $this->userDevice->create($this->userDevice->setData($attributes));
                break;
        }
    }
}
