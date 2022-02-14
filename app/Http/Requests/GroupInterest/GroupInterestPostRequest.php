<?php

namespace App\Http\Requests\GroupInterest;

use Illuminate\Foundation\Http\FormRequest;

class GroupInterestPostRequest extends FormRequest
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
            "interests" => "required|array|max:4|min:4",
            "interests.*" => "required|exists:interests,id",
        ];
    }
}
