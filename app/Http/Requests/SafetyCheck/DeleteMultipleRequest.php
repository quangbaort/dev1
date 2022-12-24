<?php

namespace App\Http\Requests\SafetyCheck;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ids.*' => ['required','uuid'],
        ];
    }
}