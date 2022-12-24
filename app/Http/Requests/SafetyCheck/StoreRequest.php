<?php

namespace App\Http\Requests\SafetyCheck;

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
            'organization_id' => ['required', 'uuid'],
            'department_id'   => ['required', 'uuid'],
            'users'           => ['required', 'array'],
            'users.*'         => ['required', 'uuid'],
            'title'           => ['required', 'max: 225'],
            'detail'          => ['max: 1023'],
            'detail_url'      => ['max: 1023'],
            'repeat'          => ['max:3', 'numeric'],
            'repeat_week'     => ['max:15'],
            'repeat_date'     => ['numeric', 'between:1,31'],
            'repeat_start'    => ['date_format:Y/m/d'],
            'repeat_end'      => ['date_format:Y/m/d', 'after_or_equal:repeat_start'],
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
            'title' => trans('app.title'),
        ];
    }
}
