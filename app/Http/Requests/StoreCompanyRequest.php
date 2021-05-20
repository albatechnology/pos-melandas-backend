<?php

namespace App\Http\Requests;

use App\Enums\Import\ImportBatchType;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('company_create');
    }

    public function rules()
    {
        return [
            'company_id' => [
                'required',
                'exists:companies,id',
            ],
            'type'       => [
                'required',
                new EnumValue(ImportBatchType::class),
            ],
            'file'       => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:' . config('core.import.max_size')
            ]
        ];
    }
}
