<?php

namespace App\Http\Controllers\City;

use App\Department;
use App\Http\Controllers\ApiController;
use App\Transformers\CityTransformer;

class CityIndexController extends ApiController
{

    public function __construct()
    {
        $this->middleware('jwt:api');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/cities/{department}",
     *     summary="Trae la lista de ciudades de un departamento",
     *     tags={"Cities"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de ciudades",
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
    public function index(Department $department)
    {
        try {
            return $this->showAll(
                $department->cities()->get(),
                CityTransformer::class,
                200);
        } catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),400);
        }
    }
}
