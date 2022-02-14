<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductPostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'description' => 'required',
            'code' => 'required',
            'price_sale' => 'required|numeric',
            'price_discount' => 'sometimes|numeric',
            'preparation_time' => 'required',
            'photo' => 'sometimes|required|image'
        ];
    }
}
