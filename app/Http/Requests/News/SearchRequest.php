<?php

namespace App\Http\Requests\News;

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
            'id' => ['nullable'],
            'user_id' => ['nullable'],
        ];
    }
}
