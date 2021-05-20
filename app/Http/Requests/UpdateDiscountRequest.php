<?php

namespace App\Http\Requests;

use App\Models\Discount;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateDiscountRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('discount_edit');
    }

    public function rules()
    {
        return [
            'description'          => [
                'string',
                'nullable',
            ],
            'type'                 => [
                'required',
            ],
            'activation_code'      => [
                'string',
                'min:4',
                'nullable',
            ],
            'value'                => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'start_time'           => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
            'end_time'             => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
            'max_use_per_customer' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'company_id'           => [
                'required',
                'integer',
            ],
        ];
    }
}
