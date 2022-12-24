<?php

namespace App\Http\Requests\Memo;

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
            'title' => ['nullable', 'max:' . FORM_INPUT_MAX_LENGTH],
        ];
    }
}
