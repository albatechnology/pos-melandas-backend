<?php

namespace App\Http\Requests;

use App\Enums\UserType;
use App\Models\User;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('user_edit');
    }

    public function rules()
    {
        return [
            'name'        => [
                'string',
                'required',
            ],
            'email'       => [
                'required',
                'unique:users,email,' . request()->route('user')->id,
            ],
//            'roles.*'     => [
//                'integer',
//            ],
//            'roles'       => [
//                'required',
//                'array',
//            ],
            'companies.*' => [
                'integer',
            ],
            'companies'   => [
                'array',
            ],
            'channels.*'  => [
                'integer',
            ],
            'channels'    => [
                'array',
            ],
        ];
    }
}
