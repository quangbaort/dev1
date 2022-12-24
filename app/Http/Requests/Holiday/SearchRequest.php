<?php

namespace App\Http\Requests\Holiday;

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
            'organization_id' => ['nullable', 'uuid'],
            'name' => ['nullable', 'max:' . FORM_INPUT_MAX_LENGTH],
            'date' => ['nullable', 'date'],
        ];
    }
}
