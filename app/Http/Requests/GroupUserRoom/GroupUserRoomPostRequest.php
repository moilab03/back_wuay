<?php

namespace App\Http\Requests\GroupUserRoom;

use Illuminate\Foundation\Http\FormRequest;

class GroupUserRoomPostRequest extends FormRequest
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
            'group_user_id' => 'required|exists:group_users,id'
        ];
    }
}
