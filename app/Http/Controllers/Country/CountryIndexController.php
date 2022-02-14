<?php

namespace App\Http\Controllers\Country;

use App\Country;
use App\Http\Controllers\ApiController;
use App\Transformers\CountryTransformer;

class CountryIndexController extends ApiController
{
    protected $country;

    public function __construct(Country $country)
    {
        $this->middleware('jwt:api');
        $this->country = $country;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/countries",
     *     summary="Trae la lista de paises",
     *     tags={"Country"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de de paises",
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
    public function index()
    {
        try {
            return $this->showAll($this->country->all(), CountryTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
