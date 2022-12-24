<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'organization_id' => ['required', 'uuid'],
            'parent_id' => ['nullable', 'uuid'],
            'name'            => ['nullable', 'max:' . FORM_INPUT_MAX_LENGTH]
        ];
    }
}
