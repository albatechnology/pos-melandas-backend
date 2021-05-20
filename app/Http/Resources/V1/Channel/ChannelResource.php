<?php

namespace App\Http\Resources\V1\Channel;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use App\Http\Resources\V1\Company\CompanyResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ChannelResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("name", Schema::TYPE_STRING, 'Toko ABC'),
//            ResourceData::makeRelationship("company", CompanyResource::class),
//            ResourceData::makeRelationship("category", ChannelCategoryResource::class, 'channelCategory'),
        ];
    }
}
