<?php

namespace App\Http\Requests;

use App\Models\PaymentType;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatePaymentTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('payment_type_edit');
    }

    public function rules()
    {
        return [
            'name'       => [
                'string',
                'required',
            ],
            'code'       => [
                'string',
                'nullable',
            ],
            'company_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
