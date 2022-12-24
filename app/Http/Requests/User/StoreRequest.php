<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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
            'name'            => ['required', 'max:' . FORM_INPUT_MAX_LENGTH],
            'name_kana'       => ['required', 'max:' . FORM_INPUT_MAX_LENGTH],
            'email'           => [
                'required_if:id, null', 'email', 'max:' . FORM_INPUT_MAX_LENGTH,
            ],
            'company_id'      => ['required', 'uuid'],
            'organization_id' => ['required', 'uuid'],
            'departments.*'   => ['required', 'uuid'],
            'is_super_admin'  => ['boolean'],
            'role'            => [Rule::in([ADMIN_ROLE, USER_ROLE])],
            'file'            => ['mimes:jpg, jpeg, png', 'max:20000'],
            'password'        => [
                'required_with:password_confirmation',
                'confirmed',
                Password::min(8),
            ],
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
            'name'      => trans('app.name'),
            'name_kana' => trans('app.furigana'),
        ];
    }
}
