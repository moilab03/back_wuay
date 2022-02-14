<?php

namespace App\Http\Controllers\ProductVariant;

use App\Commerce;
use App\Http\Controllers\ApiController;
use App\Http\Requests\ProductVariant\ProductVariantPutRequest;
use App\Product;
use App\ProductVariant;

class ProductVariantUpdateController extends ApiController
{

    public function __construct()
    {
        $this->middleware('commerce');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/variantProducts/{product_variant}",
     *     summary="Actualiza una variante de producto",
     *     tags={"Product Variant"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "price"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Devuelve una variante de producto",
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
    function update(ProductVariantPutRequest $request, ProductVariant $productVariant)
    {
        try {
            $this->validateUser($productVariant);
            $productVariant = $productVariant->setUpdate($request);
            $productVariant->save();
            return $this->showOne($productVariant, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    function validateUser($productVariant)
    {
        $commerce = Product::find($productVariant->product_id)->commerce_id;
        if(Commerce::find($commerce)->user_id !== auth()->user()->id){
            throw new \Exception('No puedes actualizar esta variación, no pertenece a su comercio');
        }
    }
}
