<?php

namespace App\Http\Controllers\Commerce;

use App\Commerce;
use App\Http\Controllers\ApiController;

class CommerceShowController extends ApiController
{

    /**
     * @OA\Get(
     *     path="/api/v1/commerces/user/{commerce}",
     *     summary="Trae un comercio",
     *     tags={"Commerces"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un comercio",
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
    function show(Commerce $commerce)
    {
        try {
            return $this->showOne($commerce,200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),400);
        }
    }
}
