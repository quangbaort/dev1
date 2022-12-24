<?php

namespace App\Http\Requests\Event;

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
            'board_id'   => ['required', 'array'],
            'board_id.*' => ['uuid'],
            'start'      => ['required', 'date'],
            'end'        => ['required', 'date'],
        ];
    }
}
