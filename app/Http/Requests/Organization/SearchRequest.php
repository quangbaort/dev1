<?php

namespace App\Http\Requests\Organization;

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
            'name'          => ['nullable', 'max:' . FORM_INPUT_MAX_LENGTH],
            'simple_name'   => ['nullable', 'max:' . FORM_INPUT_MAX_LENGTH],
            'name_kana'     => ['nullable', 'max:' . FORM_INPUT_MAX_LENGTH],
            'prefecture'    => ['nullable', 'max:15'],
            'city'          => ['nullable', 'max:15'],
            'street'        => ['nullable', 'max:127'],
            'building'      => ['nullable', 'max:127'],
            'tel'           => ['nullable', 'numeric', 'max:11'],
        ];
    }
}
