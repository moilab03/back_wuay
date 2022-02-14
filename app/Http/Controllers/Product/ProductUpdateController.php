<?php

namespace App\Http\Controllers\Product;

use App\Commerce;
use App\Http\Requests\Product\ProductPostRequest;
use App\Product;
use App\Resource;
use App\TypeResource;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductUpdateController extends ApiController
{

    protected $resource;
    protected $typeResource;

    public function __construct(Resource $resource, TypeResource $typeResource)
    {
        $this->middleware('commerce');
        $this->resource = $resource;
        $this->typeResource = $typeResource;
    }



    /**
     * @OA\Put(
     *     path="/api/v1/products/{product}",
     *     summary="Actualiza un producto",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "category_id","description","code", "price_sale", "preparation_time"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="category_id",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string"
     *                 ),
     *      @OA\Property(
     *                     property="code",
     *                     type="string"
     *                 ),
     *      @OA\Property(
     *                     property="price_sale",
     *                     type="string"
     *                 ),
     *      @OA\Property(
     *                     property="price_discount",
     *                     type="string"
     *                 ),
     *      @OA\Property(
     *                     property="preparation_time",
     *                     type="string"
     *                 ),
     *          @OA\Property(
     *                     property="photo",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un product",
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
    function update(ProductPostRequest $request, Product $product)
    {
        DB::beginTransaction();
        try {
            $commerce = Commerce::find($product->commerce_id);
            $commerce->validateUserAdministrator();
            $product= $product->updateData($request);
            $product->save();
            if ($request->has('photo')) {
                $this->deleteResource(TypeResource::PHOTO_PRODUCT, $product);
                $this->storeResource($request->photo, TypeResource::PHOTO_PRODUCT, 'products/photo', true, $product);
            }
            DB::commit();
            return $this->showOne($product, 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/products/status/{product}",
     *     summary="Cambia el estado de un producto",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un usuario",
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
    function changeState(Product $product)
    {
        try {
            $commerce = Commerce::find($product->commerce_id);
            $commerce->validateUserAdministrator();
            $product = $product->changeStatus();
            $product->save();
            return $this->showOne($product, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    public function deleteResource($typePackage, $product)
    {
        try {
            $resource = $product->resource()
                ->byTypeResource($typePackage)
                ->first();
            if ($resource) {
                $resource->delete();
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

    }

    protected function storeResource($resource, $typePackage, $path, $isImage, $product)
    {
        try {
            $this->resource->create($this->resource->saveResource(
                $resource,
                Product::class,
                $product->id,
                $this->typeResource->byType($typePackage)->value('id'),
                $path,
                $isImage
            ));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
