<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductBankPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
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
        ];
    }
}
