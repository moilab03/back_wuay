<?php

namespace App\Transformers;

use App\Product;
use App\ProductVariant;
use App\Status;
use App\User;
use League\Fractal\TransformerAbstract;

class ProductVariantTransformer extends TransformerAbstract
{

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];


    function getStatus($productVariant)
    {
        $status = $productVariant->status()->first();
        return [
            'id' => $status->id,
            'status' => $status->status,
            'is_enabled' => $status->status === Status::ENABLED,
            'label' => $status->status === Status::ENABLED ? 'Activo' : 'Inactivo'
        ];
    }

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(ProductVariant $productVariant)
    {
        return [
            'id' => $productVariant->id,
            'price' => $productVariant->price,
            'name' => $productVariant->name,
            'status' => $this->getStatus($productVariant)
        ];
    }
}
