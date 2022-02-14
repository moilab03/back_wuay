<?php

namespace App\Http\Requests\Auth;

use App\Rol;
use Illuminate\Foundation\Http\FormRequest;

class AuthAdministratorRequest extends FormRequest
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
        $admin = Rol::ADMINISTRATOR;
        $adminCommerce = Rol::ADMINISTRATOR_COMMERCE;
        
        return [
            'email' => 'required|email',
            'password' => 'required',
            'rol' => "required|in:{$admin},{$adminCommerce}"
        ];
    }
}
