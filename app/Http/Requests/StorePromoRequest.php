<?php

namespace App\Http\Requests;

use App\Models\Promo;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorePromoRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('promo_create');
    }

    public function rules()
    {
        return [
            'name'                  => [
                'string',
                'nullable',
            ],
            'description'           => [
                'required',
            ],
            'start_date'            => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'end_date'              => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'promotable_type'       => [
                'string',
                'nullable',
            ],
            'promotable_identifier' => [
                'string',
                'nullable',
            ],
            'company_id'            => [
                'required',
                'integer',
            ],
        ];
    }
}
