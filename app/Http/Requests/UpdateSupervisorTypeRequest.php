<?php

namespace App\Http\Requests;

use App\Models\SupervisorType;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSupervisorTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('supervisor_type_edit');
    }

    public function rules()
    {
        return [
            'name'  => [
                'string',
                'required',
            ],
            'level' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
