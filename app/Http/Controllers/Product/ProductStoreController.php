<?php

namespace App\Http\Controllers\Product;

use App\Http\Requests\Product\ProductBankPostRequest;
use App\Product;
use App\Commerce;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Product\ProductPostRequest;
use App\ProductVariant;
use App\Resource;
use App\TypeResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductStoreController extends ApiController
{

    protected $product;
    protected $resource;
    protected $typeResource;

    public function __construct(Product $product, Resource $resource, TypeResource $typeResource)
    {
        $this->middleware('commerce');
        $this->product = $product;
        $this->resource = $resource;
        $this->typeResource = $typeResource;
    }


    /**
     * @OA\Post(
     *     path="/api/v1/products/{commerce}",
     *     summary="Crea un prodcuto asociado a un comercio",
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
    function store(ProductPostRequest $request, Commerce $commerce)
    {
        DB::beginTransaction();
        try {
            $commerce->validateUserAdministrator();
            $this->product = $this->product->create($this->product->setData($request, $commerce));
            if ($request->has('photo'))
                $this->storeResource($request->photo, TypeResource::PHOTO_PRODUCT, 'products/photo');
            DB::commit();
            return $this->showOne($this->product, 200);
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
                Product::class,
                $this->product->id,
                $this->typeResource->byType($typePackage)->value('id'),
                $path,
                $isImage
            ));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }


    /**
     * @OA\Post(
     *     path="/api/v1/products/bank/{commerce}/{product}?include=productVariants",
     *     summary="Agrega un producto del banco al comercio",
     *     tags={"Products"},
     *     *     @OA\RequestBody(
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
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un producto",
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
    function storeForBank(ProductBankPostRequest $request, Commerce $commerce, Product $product)
    {
        DB::beginTransaction();
        try {
            $commerce->validateUserAdministrator();
            $product->validateIfHasBank();
            $this->validateIfExists($product, $commerce);
            $productNew = $this->product->create($this->product->setDataBank($request, $commerce, $product));
            $this->product = $productNew;
            $this->savePhoto($product);
            $product->productVariants()->get()->map(function ($variant) use ($productNew) {
                $productVariant = new ProductVariant();
                ProductVariant::create($productVariant->setDataVariant($productNew, $variant));
            });
            DB::commit();
            return $this->showOne($productNew, 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    protected function validateIfExists($product, $commerce)
    {
        if ($commerce->products()->where('product_bank_id', $product->id)->count() > 0)
            throw new \Exception('Ya ha agregado este producto del banco');
    }

    protected function savePhoto($product)
    {
        try {
            $url = $product->resource()
                ->byTypeResource(TypeResource::PHOTO_PRODUCT)
                ->value('url');
            if ($url) {
                $this->storeResource(env('URL_STORAGE').$url, TypeResource::PHOTO_PRODUCT, 'products/photo', true);
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

}
