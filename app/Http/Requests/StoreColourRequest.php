<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreColourRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('colour_create');
    }

    public function rules()
    {
        return [
            'name'             => [
                'string',
                'required',
            ],
            'code'             => [
                'string',
                'required',
            ],
            'product_brand_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
