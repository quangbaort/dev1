<?php

namespace App\Http\Requests\SafetyCheck;

use Illuminate\Foundation\Http\FormRequest;

class FilterChartRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'startDate' => ['date'],
            'endDate'   => ['date', 'after_or_equal:startDate'],
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
            'startDate' => trans('app.start_date'),
            'endDate' => trans('app.end_date'),
        ];
    }
}
