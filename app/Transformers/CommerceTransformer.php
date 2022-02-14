<?php

namespace App\Transformers;

use App\Commerce;
use App\Country;
use App\Department;
use App\Status;
use App\TypeResource;
use League\Fractal\TransformerAbstract;

class CommerceTransformer extends TransformerAbstract
{


    protected function getResource($typeResource, $commerce)
    {
        $resource = $commerce->resource()
            ->byTypeResource($typeResource)
            ->first();
        if ($resource)
            return [
                'id' => $resource->id,
                'url' => env('URL_STORAGE') . $resource->url
            ];
        return [];
    }

    protected function getCity($commerce)
    {
        $city = $commerce->city()->first();
        $department = Department::find($city->department_id);
        $country = Country::find($department->country_id);
        return [
            'id' => $city->id,
            'city' => $city->city,
            'department' => [
                'id' => $department->id,
                'department' => $department->department
            ],
            'country' => [
                'id' => $country->id,
                'country' => $country->country
            ]
        ];
    }


    function getStatus($commerce)
    {
        $status = $commerce->status()->first();
        return [
            'id' => $status->id,
            'status' => $status->status,
            'is_enabled' => $status->status === Status::ENABLED,
            'label' => $status->status === Status::ENABLED ? 'Activo' : 'Inactivo'
        ];
    }

    public function transform(Commerce $commerce)
    {
        return [
            'id' => $commerce->id,
            'name' => $commerce->name,
            'nit' => $commerce->nit,
            'contact' => $commerce->contact,
            'email' => $commerce->email,
            'address' => $commerce->address,
            'phone' => $commerce->phone,
            'web' => $commerce->web,
            'latitude' => $commerce->latitude,
            'longitude' => $commerce->longitude,
            'attention_schedule' => $commerce->attention_schedule,
            'quantity_table' => $commerce->quantity_table,
            'city' => $this->getCity($commerce),
            'logo' => $this->getResource(TypeResource::LOGO_COMMERCE, $commerce),
            'banner' => $this->getResource(TypeResource::COVER_COMMERCE, $commerce),
            'qr_menu' => $this->getResource(TypeResource::QR_TABLE, $commerce),
            'qr_game' => $this->getResource(TypeResource::QR_SECURITY, $commerce)
        ];
    }
}
