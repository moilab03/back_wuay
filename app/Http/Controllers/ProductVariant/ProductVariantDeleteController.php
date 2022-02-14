<?php

namespace App\Http\Controllers\ProductVariant;

use App\Commerce;
use App\Http\Controllers\ApiController;
use App\Product;
use App\ProductVariant;

class ProductVariantDeleteController extends ApiController
{

    /**
     * @OA\Delete(
     *     path="/api/v1/variantProducts/{product_variant}",
     *     summary="Cambia el estado de una variación de producto",
     *     tags={"Product Variant"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una variación de producto",
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
    function delete(ProductVariant $productVariant)
    {
        try {
            $productVariant = $productVariant->changeStatus();
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
