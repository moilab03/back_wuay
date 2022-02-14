<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Rol;
use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;

class UserIndexController extends ApiController
{
    protected $user;

    public function __construct(User $user)
    {
        $this->middleware('administrator');
        $this->user = $user;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/commerce",
     *     summary="Trae la lista de usuarios administradores de comercios",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de usuarios",
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
    function indexUserCommerce(Request $request)
    {
        try {
           // $quantity = $request->get('quantity', 15);
            $this->user = $this->user
                ->byRol(Rol::ADMINISTRATOR_COMMERCE)
                ->get();
            return $this->showAll($this->user, UserTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
