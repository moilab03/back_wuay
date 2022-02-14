<?php

namespace App\Transformers;

use App\Product;
use App\Status;
use App\TypeResource;
use League\Fractal\TransformerAbstract;
use League\Fractal\ParamBag;

class ProductTransformer extends TransformerAbstract
{


    private $validParams = ['enabled'];


    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'productVariants'
    ];

    function getCategory($product)
    {
        $category = $product->category()->first();
        return [
            'id' => $category->id,
            'status' => $category->category,
        ];
    }

    function getStatus($product)
    {
        $status = $product->status()->first();
        return [
            'id' => $status->id,
            'status' => $status->status,
            'is_enabled' => $status->status === Status::ENABLED,
            'label' => $status->status === Status::ENABLED ? 'Activo' : 'Inactivo'
        ];
    }


    protected function getResource($typeResource, $product)
    {
        $resource = $product->resource()
            ->byTypeResource($typeResource)
            ->first();
        if ($resource)
            return [
                'id' => $resource->id,
                'url' => env('URL_STORAGE') . $resource->url,
                'has_photo' => true
            ];
        return [
            'has_photo' => false
        ];
    }


    function includeProductVariants(Product $product, ParamBag $params = null)
    {
        list($enabled) = $params->get('enabled');
        $variants = !$enabled ? $product->productVariants()->get() : $product->productVariants()->enabled()->get();
        if ($variants)
            return $this->collection($variants, new ProductVariantTransformer());
    }

    public function transform(Product $product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'code' => $product->code,
            'price_sale' => $product->price_sale,
            'price_discount' => $product->price_discount,
            'price_to_show' => ($product->price_discount === 0 && ($product->price_sale < $product->price_discount)) ? $product->price_sale : $product->price_discount,
            'preparation_time' => $product->preparation_time,
            'status' => $this->getStatus($product),
            'category' => $this->getCategory($product),
            'photo' => $this->getResource(TypeResource::PHOTO_PRODUCT, $product),
        ];
    }
}
