<?php

namespace App\Http\Requests\Event;

use App\Rules\StorageAvailRule;
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
            'title'    => ['required'],
            'organization_id'    => ['required', 'uuid'],
            'department_id'      => ['required', 'uuid'],
            'departments.*'      => ['required', 'uuid'],
            'users'              => ['required', 'array'],
            'users.*'            => ['uuid'],
            'is_allday'          => ['boolean'],
            'is_general_meeting' => ['boolean'],
            'repeat'             => ['integer', 'max:3'],
            'repeat_date'        => ['integer', 'between:1,31'],
            'repeat_start'       => ['date_format:Y-m-d'],
            'start'              => ['required', 'date_format:Y-m-d H:i:s'],
            'end'                => ['required', 'after_or_equal:start', 'date_format:Y-m-d H:i:s'],
            'repeat_end'         => ['after_or_equal:repeat_start'],
            'period'             => ['required', 'before_or_equal:end', 'date_format:Y-m-d'],
            'files'             =>  ['array', new StorageAvailRule()],
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
