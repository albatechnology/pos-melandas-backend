<?php

namespace App\Http\Requests\API\V1\Lead;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Enums\LeadType;
use App\Models\Lead;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CreateLeadRequest extends BaseApiRequest
{
    protected ?string $model = Lead::class;

    public static function data(): array
    {
        return [
            RequestData::makeEnum('type', LeadType::class, true),
            RequestData::make('label', Schema::TYPE_STRING, 'My Leads', 'nullable|string|min:2|max:100'),
            RequestData::make('customer_id', Schema::TYPE_INTEGER, 1, 'required|exists:customers,id'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}