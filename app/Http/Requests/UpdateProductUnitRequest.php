<?php

namespace App\Http\Requests;

use App\Models\ProductUnit;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateProductUnitRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_unit_edit');
    }

    public function rules()
    {
        return [
            'product_id' => [
                'required',
                'integer',
            ],
            'name'       => [
                'string',
                'required',
            ],
            'detail'     => [
                'string',
                'nullable',
            ],
        ];
    }
}
