<?php

namespace App\Http\Requests;

use App\Models\Shipment;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreShipmentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('shipment_create');
    }

    public function rules()
    {
        return [
            'order_id'                => [
                'required',
                'integer',
            ],
            'status'                  => [
                'required',
            ],
            'reference'               => [
                'string',
                'nullable',
            ],
            'estimated_delivery_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'arrived_at'              => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
        ];
    }
}
