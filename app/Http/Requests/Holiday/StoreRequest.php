<?php

namespace App\Http\Requests\Holiday;

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
            'id' => ['uuid'],
            'organization_id' => ['required', 'uuid'],
            'name' => ['required', 'max:' . FORM_INPUT_MAX_LENGTH],
            'date' => ['required', 'date'],
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
            'name' => trans('app.holiday_name'),
        ];
    }
}
