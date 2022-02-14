<?php

namespace App\Transformers;

use App\Department;
use League\Fractal\TransformerAbstract;

class DepartmentTransformer extends TransformerAbstract
{
    public function transform(Department $department)
    {
        return [
            'id' => $department->id,
            'department' => $department->department
        ];
    }
}
