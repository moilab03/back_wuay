<?php

namespace App\Http\Requests\Commerce;

use Illuminate\Foundation\Http\FormRequest;

class CommercePostRequest extends FormRequest
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
            'name' => 'required|unique:commerces,name',
            'nit' => 'required|unique:commerces,nit',
            'contact' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'phone' => 'required|numeric',
            'latitude' => 'required',
            'longitude' => 'required',
            'attention_schedule' => 'required',
            'quantity_table' => 'required',
            'city_id' => 'required|exists:cities,id',
            'logo' => 'required|image',
            'banner' => 'required|image'
        ];
    }
}
