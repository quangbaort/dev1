<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ExportUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'userIds' => ['required'],
            'userIds.*' => ['uuid']
        ];
    }
}
