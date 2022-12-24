<?php

namespace App\Http\Requests\NotifyGroup;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'      => ['uuid'],
            'user_id' => ['required', 'array'],
            'user_id.*' => ['uuid'],
            'name'    => ['required'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => trans('app.name'),
        ];
    }
}
