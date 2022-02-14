<?php

namespace App\Http\Controllers\ProductVariant;

use App\Commerce;
use App\Product;
use App\ProductVariant;
use App\Http\Controllers\ApiController;
use App\Http\Requests\ProductVariant\ProductVariantPostRequest;


class ProductVariantStoreController extends ApiController
{
    protected $productVariant;

    function __construct(ProductVariant $productVariant)
    {
        $this->middleware('commerce');
        $this->productVariant = $productVariant;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/variantProducts",
     *     summary="Agrega una variante de producto",
     *     tags={"Product Variant"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "price", "product_id"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="string",
     *                 ),
     *                @OA\Property(
     *                     property="product_id",
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
    function store(ProductVariantPostRequest $request)
    {
        try {
            if (auth()->user()->id !== Commerce::find(
                    Product::find($request->product_id)->commerce_id
                )->user_id) {
                throw new \Exception('No puedes agregar variaciones a este producto, no pertenece a su comercio');
            }
            $this->productVariant = $this->productVariant->create(
                $this->productVariant->setData($request->all())
            );
            return $this->showOne($this->productVariant, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
