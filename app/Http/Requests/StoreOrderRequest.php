<?php

namespace App\Http\Requests;

use App\Models\Order;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('order_create');
    }

    public function rules()
    {
        return [
            'customer_id' => [
                'required',
                'integer',
            ],
            'address_id'  => [
                'required',
                'integer',
            ],
            'channel_id'  => [
                'required',
                'integer',
            ],
            'reference'   => [
                'string',
                'required',
            ],
            'status'      => [
                'required',
            ],
            'price'       => [
                'required',
            ],
        ];
    }
}
