<?php

namespace App\Http\Requests\Department;

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
            'id'              => ['uuid'],
            'organization_id' => ['required'],
            'parent_id' => ['nullable'],
            'name'            => ['required', 'max:' . FORM_INPUT_MAX_LENGTH],
            'name_kana'       => ['required', 'max:' . FORM_INPUT_MAX_LENGTH],
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
            'name' => trans('app.name_title'),
            'name_kana' => trans('app.name_title_kana'),
        ];
    }
}
