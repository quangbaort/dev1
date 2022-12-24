<?php

namespace App\Http\Requests\Memo;

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
            'id' => ['uuid'],
            'organization_id' => ['required', 'uuid'],
            'start' => ['required', 'date_format:"Y-m-d H:i:s"', 'before_or_equal:end'],
            'end' => ['required', 'date_format:"Y-m-d H:i:s"'],
            'title' => ['required', 'max:' . FORM_INPUT_MAX_LENGTH],
            'detail' => ['nullable', 'max:1023'],
            'theme_color' => ['nullable', 'max:15'],
            'icon' => ['nullable', 'max:255'],
            'image_s3_url' => ['nullable', 'max:1023'],
            'url' => ['nullable', 'max:1023'],
            'file' => ['nullable', new StorageAvailRule() ],
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
