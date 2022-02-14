<?php

namespace App\Http\Controllers\Commerce;

use App\Commerce;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Commerce\CommercePostRequest;
use App\Resource;
use App\Services\QrService;
use App\TypeResource;
use Illuminate\Support\Facades\DB;


class CommerceStoreController extends ApiController
{
    protected $commerce;
    protected $qr;
    protected $resource;
    protected $typeResource;

    public function __construct(Commerce $commerce, QrService $qr, Resource $resource, TypeResource $typeResource)
    {
        $this->middleware('commerce');
        $this->commerce = $commerce;
        $this->qr = $qr;
        $this->resource = $resource;
        $this->typeResource = $typeResource;
    }


    /**
     * @OA\Post(
     *     path="/api/v1/commerces",
     *     summary="Crea un comercio asociado aun usuario administrador del comercio",
     *     tags={"Commerces"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "nit", "contact","email","address", "phone", "latitude", "longitude", "attention_schedule","quantity_table","city_id","logo","banner"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="nit",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="contact",
     *                     type="string"
     *                 ),
     *      @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *      @OA\Property(
     *                     property="address",
     *                     type="string"
     *                 ),
     *      @OA\Property(
     *                     property="phone",
     *                     type="string"
     *                 ),
     *      @OA\Property(
     *                     property="latitude",
     *                     type="string"
     *                 ),
     *     @OA\Property(
     *                     property="longitude",
     *                     type="string"
     *                 ),
     *     @OA\Property(
     *                     property="attention_schedule",
     *                     type="string"
     *                 ),
     *      @OA\Property(
     *                     property="quantity_table",
     *                     type="string"
     *                 ),
     *       @OA\Property(
     *                     property="city_id",
     *                     type="string"
     *                 ),
     *     @OA\Property(
     *                     property="logo",
     *                     type="string"
     *                 ),
     *      @OA\Property(
     *                     property="banner",
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
    function store(CommercePostRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->validateIfHasCommerce();
            $this->commerce = $this->commerce->create(
                $this->commerce->setDataCommerce($request)
            );

            $this->storeResource($this->qr->generateQR(
                "https://menu.wuay.com.co/home/" . $this->commerce->id),
                TypeResource::QR_TABLE,
                'qr/game',
                false);

            $this->storeResource($this->qr->generateQR(
                "https://menu.wuay.com.co/game/" . $this->commerce->id),
                TypeResource::QR_SECURITY,
                'qr/menu',
                false);

            $this->storeResource($request->logo, TypeResource::LOGO_COMMERCE, 'commerces/logos');
            $this->storeResource($request->banner, TypeResource::COVER_COMMERCE, 'commerces/banner');
            DB::commit();
            return $this->showOne($this->commerce, 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    protected function storeResource($resource, $typePackage, $path, $isImage = true)
    {
        try {
            $this->resource->create($this->resource->saveResource(
                $resource,
                Commerce::class,
                $this->commerce->id,
                $this->typeResource->byType($typePackage)->value('id'),
                $path,
                $isImage
            ));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    protected function validateIfHasCommerce()
    {
        if (auth()->user()->commerce()->count() > 0) {
            throw new \Exception('Ya tienes creado un comercio, solo puedes actualizar');
        }
    }
}
