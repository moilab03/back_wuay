<?php

namespace App\Transformers;

use App\Category;
use App\Status;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{

    function getStatus($category)
    {
        $status = $category->status()->first();
        return [
            'id' => $status->id,
            'status' => $status->status,
            'is_enabled' => $status->status === Status::ENABLED,
            'label' => $status->status === Status::ENABLED ? 'Activo' : 'Inactivo'
        ];
    }

    public function transform(Category $category)
    {
        return [
            'id' => $category->id,
            'category' => $category->category,
            'status' => $this->getStatus($category)
        ];
    }
}
