<?php

namespace App\Http\Requests\EventReply;
use Illuminate\Foundation\Http\FormRequest;

class AnswerRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token'         => ['required', 'uuid' , 'exits:users,id'],
            'answer'        => ['required', 'max:3', 'between:1,3'],
            'event'         => ['required', 'uuid' , 'exits:events,id'],
        ];
    }
}
