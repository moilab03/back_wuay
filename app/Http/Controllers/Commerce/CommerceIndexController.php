<?php

namespace App\Http\Controllers\Commerce;

use App\Commerce;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Commerce\CommerceByDistancePostRequest;
use App\Transformers\CommerceTransformer;

class CommerceIndexController extends ApiController
{
    protected $commerce;

    public function __construct(Commerce $commerce)
    {
        $this->commerce = $commerce;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/commerces/game",
     *     summary="Obtiene los comercios cercanos si se le pasa una latitude y longitud, ademas se debe enviar with_location=true si el usuario acepta compartir la localización",
     *     tags={"Commerces"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"with_location"},
     *                 @OA\Property(
     *                     property="with_location",
     *                     type="boolean",
     *                 ),
     *                 @OA\Property(
     *                     property="latitude",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="longitude",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de comercios",
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
    function getByDistance(CommerceByDistancePostRequest $request)
    {
        try {
            if ($request->with_location)
                $this->commerce = $this->commerce
                    ->distance($request->latitude, $request->longitude)
                    ->orderBy('distance', 'ASC')
                    ->get()
                    ->take(5);
            else
                $this->commerce = $this->commerce
                    ->get();
            return $this->showAll($this->commerce, CommerceTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
