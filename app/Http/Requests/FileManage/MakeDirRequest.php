<?php

namespace App\Http\Requests\FileManage;

use Illuminate\Foundation\Http\FormRequest;

class MakeDirRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => ['required'],
            'parent_id' => ['nullable', 'uuid'],
        ];
    }
}