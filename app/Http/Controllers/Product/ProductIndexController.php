<?php

namespace App\Http\Controllers\Product;

use App\Category;
use App\Commerce;
use App\Http\Controllers\ApiController;
use App\Product;
use App\Status;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;


class ProductIndexController extends ApiController
{
    protected $product;
    protected $commerce;

    public function __construct(Product $product, Commerce $commerce)
    {
        $this->middleware('commerce')
            ->only(['indexForCommerce','indexBankProducts']);
        $this->product = $product;
        $this->commerce = $commerce;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/products/menu/{category}?include=productVariants:enabled(1)",
     *     summary="Trae la lista de productos asociados a una categoria, para la pagina web",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de productos",
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
    function indexUser(Category $category)
    {
        try {
            $products = $category->products()
                ->byStatus(Status::byStatus(Status::ENABLED)->value('id'))
                ->get();
            return $this->showAll($products, ProductTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/products/commerce/{commerce}?include=productVariants",
     *     summary="Trae la lista de productos asociados a un comercio",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de productos",
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
    function indexForCommerce(Request $request, Commerce $commerce)
    {
        try {
            $commerce->validateUserAdministrator();
            // $quantity = $request->get('quantity', 15);
            $this->product = $commerce
                ->products()
                ->get();
            return $this->showAll($this->product, ProductTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/products/bank/{commerce}?include=productVariants&&category=all - id_category",
     *     summary="Trae la lista de productos del banco excepto los que ya ha agregado",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de productos",
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
    function indexBankProducts(Request $request,Commerce $commerce)
    {
        try {
            $filter  = $request->get('category');
            $commerce->validateUserAdministrator();
            $ids = $commerce->products()
                ->whereNotNull('product_bank_id')
                ->pluck('product_bank_id');

            $this->commerce = $this->commerce->find(8);
            if($filter === 'all'){
                $this->product = $this->commerce->products()
                    ->whereNotIn('id', $ids)
                    ->get();
                return $this->showAll($this->product, ProductTransformer::class, 200);
            }else {
                $this->product = $this->commerce->products()
                    ->whereNotIn('id', $ids)
                    ->where('category_id', $filter)
                    ->get();
                return $this->showAll($this->product, ProductTransformer::class, 200);
            }
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
