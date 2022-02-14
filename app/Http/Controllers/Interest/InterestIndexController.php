<?php

namespace App\Http\Controllers\Interest;

use App\Http\Controllers\ApiController;
use App\Interest;
use App\Status;
use App\Transformers\InterestTransformer;

class InterestIndexController extends ApiController
{
    protected $interest;

    public function __construct(Interest $interest)
    {
        $this->middleware('jwt:api');
        $this->interest = $interest;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/interests",
     *     summary="Trae la lista de intereses",
     *     tags={"Country"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de intereses",
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
    function index()
    {
        try {
            $this->interest = $this->interest->byStatus(Status::ENABLED)->get();
            return $this->showAll($this->interest,InterestTransformer::class,200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
