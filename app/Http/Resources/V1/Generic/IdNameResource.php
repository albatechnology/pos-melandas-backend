<?php

namespace App\Http\Resources\V1\Generic;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use App\Http\Resources\V1\Channel\ChannelResource;
use App\Http\Resources\V1\Company\CompanyResource;
use App\Http\Resources\V1\Generic\MediaResource;
use App\OpenApi\Schemas\MediaSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class IdNameResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("name", Schema::TYPE_STRING, 'test name'),
        ];
    }
}
