<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserPhonePostRequest extends FormRequest
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
            'id_token_string' => 'required',
            'commerce_id' => 'required|exists:commerces,id',
            'name' => 'required',
            'terms_and_conditions' => 'required|boolean'
        ];
    }
}
