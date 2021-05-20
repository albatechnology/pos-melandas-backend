<?php

namespace App\Http\Requests;

use App\Enums\UserType;
use App\Models\User;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('user_create');
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
                'unique:users',
            ],
            'password'    => [
                'required',
            ],
            'roles.*'     => [
                'integer',
            ],
            'roles'       => [
                'nullable',
                'array',
            ],
            'type'        => [
                'required',
                new EnumValue(UserType::class)
            ],
            'supervisor_type_id' => [
                'required_if:type,' . UserType::SUPERVISOR,
                'exists:supervisor_types,id'
            ],
            'company_id' => [
                'required',
                'exists:companies,id'
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
