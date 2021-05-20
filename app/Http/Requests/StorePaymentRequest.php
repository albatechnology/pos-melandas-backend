<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('payment_create');
    }

    public function rules()
    {
        return [
            'amount'          => [
                'required',
            ],
            'payment_type_id' => [
                'required',
                'integer',
            ],
            'reference'       => [
                'string',
                'nullable',
            ],
            'status'          => [
                'required',
            ],
            'reason'          => [
                'string',
                'nullable',
            ],
            'order_id'        => [
                'required',
                'integer',
            ],
        ];
    }
}
