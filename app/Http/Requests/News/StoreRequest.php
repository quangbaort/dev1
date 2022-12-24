<?php

namespace App\Http\Requests\News;

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
            'id'              => ['uuid'],
            'organization_id' => ['required', 'uuid'],
            'board_id'        => ['required', 'uuid'],
            'user_id'         => ['required', 'array'],
            'user_id.*'       => ['uuid'],
            'title'           => ['required', 'max:' . FORM_INPUT_MAX_LENGTH],
            'image_s3_url'    => ['nullable', 'max:1023', 'url'],
            'start'           => ['required', 'date_format:"Y-m-d H:i:s"', 'before_or_equal:end'],
            'end'             => ['required', 'date_format:"Y-m-d H:i:s"'],
            'place'           => ['nullable', 'max:255'],
            'place_url'       => ['nullable', 'max:1023'],
            'detail'          => ['nullable', 'max:1023'],
            'detail_url'      => ['nullable', 'max:1023'],
            'theme_color'     => ['nullable', 'max:15'],
            'icon'            => ['nullable', 'max:255'],
            'image'           => ['image', new StorageAvailRule()],
            'file'            => ['file', new StorageAvailRule()],
            'resend'          => ['boolean']
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
