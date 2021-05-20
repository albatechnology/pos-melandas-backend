<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductCategoryCodeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_category_code_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'code' => [
                'string',
                'nullable',
            ],
        ];
    }
}
