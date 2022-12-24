<?php

namespace App\Http\Requests\Organization;

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
            'id'          => ['uuid'],
            'name'        => ['required', 'max:' . FORM_INPUT_MAX_LENGTH],
            'simple_name' => ['required', 'max:' . FORM_INPUT_MAX_LENGTH],
            'name_kana'   => ['required', 'max:' . FORM_INPUT_MAX_LENGTH],
            'zip_code'    => ['required', 'digits:7'],
            'prefecture'  => ['required', 'max:15'],
            'city'        => ['required', 'max:15'],
            'street'      => ['required', 'max:127'],
            'building'    => ['max:127'],
            'tel'         => ['max:11'],
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
            'name' => trans('app.organization_name'),
            'name_kana' => trans('app.organization_name_furigana'),
            'simple_name' => trans('app.organization_abbreviation'),
            'prefecture' => trans('app.prefectures'),
            'city' => trans('app.municipalities'),
            'street' => trans('app.house_number'),
        ];
    }
}
