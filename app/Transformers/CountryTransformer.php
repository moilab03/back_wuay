<?php

namespace App\Transformers;

use App\Country;
use League\Fractal\TransformerAbstract;

class CountryTransformer extends TransformerAbstract
{


    public function transform(Country $country)
    {
        return [
            'id' => $country->id,
            'country' => $country->country
        ];
    }
}
