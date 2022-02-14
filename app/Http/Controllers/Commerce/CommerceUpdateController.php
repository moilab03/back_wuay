<?php

namespace App\Http\Controllers\Commerce;

use App\Commerce;
use App\Resource;
use App\Services\QrService;
use App\TypeResource;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Commerce\CommerceUpdateRequest;

class CommerceUpdateController extends ApiController
{

    protected $resource;
    protected $typeResource;
    protected $qr;

    public function __construct(Resource $resource, TypeResource $typeResource, QrService $qr)
    {
        $this->middleware('commerce')->except('updateQR');
        $this->resource = $resource;
        $this->qr = $qr;
        $this->typeResource = $typeResource;
    }


    /**
     * @OA\Put(
     *     path="/api/v1/commerces/{commerce}",
     *     summary="Actualiza un comercio asociado aun usuario administrador del comercio",
     *     tags={"Commerces"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "nit", "contact","email","address", "phone", "latitude", "longitude", "attention_schedule","quantity_table","city_id"},
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
     *
     *                 ),
     *      @OA\Property(
     *                     property="banner",
     *                     type="string"
     *
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
    function update(CommerceUpdateRequest $request, Commerce $commerce)
    {
        DB::beginTransaction();
        try {
            $this->validateIfCanValidate($commerce);
            $commerce = $commerce->setDataUpdateCommerce($request);
            if ($request->has('logo')) {
                $this->deleteResource(TypeResource::LOGO_COMMERCE, $commerce);
                $this->storeResource($request->logo, TypeResource::LOGO_COMMERCE, 'commerces/logos', true, $commerce);
            }
            if ($request->has('banner')) {
                $this->deleteResource(TypeResource::COVER_COMMERCE, $commerce);
                $this->storeResource($request->banner, TypeResource::COVER_COMMERCE, 'commerces/banner', true, $commerce);
            }
            $commerce->save();
            DB::commit();
            return $this->showOne($commerce, 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    public function updateQR(Commerce $commerce)
    {

        try {
            $logo = $commerce->resource()
                ->byTypeResource(TypeResource::LOGO_COMMERCE)
                ->value('url');
            $this->deleteResource(TypeResource::QR_TABLE, $commerce);
            $this->storeResource($this->qr->updateGenerateQR(
                "https://menu.wuay.com.co/home/" . $commerce->id, $logo),
                TypeResource::QR_TABLE,
                'qr/game',
                false,
                $commerce);
            $this->deleteResource(TypeResource::QR_SECURITY, $commerce);
            $this->storeResource($this->qr->updateGenerateQR(
                "https://menu.wuay.com.co/game/" . $commerce->id, $logo),
                TypeResource::QR_SECURITY,
                'qr/menu',
                false,
                $commerce);
            return $this->showMessage('Actualizado', 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }


    }

    protected function storeResource($resource, $typePackage, $path, $isImage, $commerce)
    {
        try {
            $this->resource->create($this->resource->saveResource(
                $resource,
                Commerce::class,
                $commerce->id,
                $this->typeResource->byType($typePackage)->value('id'),
                $path,
                $isImage
            ));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function deleteResource($typePackage, $commerce)
    {
        try {
            $resource = $commerce->resource()
                ->byTypeResource($typePackage)
                ->first();
            if ($resource) {
                $resource->delete();
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

    }

    protected function validateIfCanValidate($commerce)
    {
        if ($commerce->user_id !== auth()->user()->id)
            throw new \Exception('Este comercio no esta asociado a tu usuario');
    }
}
