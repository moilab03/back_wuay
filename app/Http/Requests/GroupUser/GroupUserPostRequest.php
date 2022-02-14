<?php

namespace App\Http\Requests\GroupUser;

use Illuminate\Foundation\Http\FormRequest;

class GroupUserPostRequest extends FormRequest
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
            'name' => 'required',
            "photos" => "sometimes|array|max:3",
            "photos.*" => "image",
        ];
    }
}
