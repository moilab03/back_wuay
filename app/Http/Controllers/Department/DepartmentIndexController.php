<?php

namespace App\Http\Controllers\Department;

use App\Country;
use App\Http\Controllers\ApiController;
use App\Transformers\DepartmentTransformer;

class DepartmentIndexController extends ApiController
{

    public function __construct()
    {
        $this->middleware('jwt:api');
    }


    /**
     * @OA\Get(
     *     path="/api/v1/departments/{country}",
     *     summary="Trae la lista de departamentos de un pais",
     *     tags={"Departments"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de departamentos",
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
    public function index(Country $country)
    {
        try {
            return $this->showAll(
                $country->departments()->get(),
                DepartmentTransformer::class,
                200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
