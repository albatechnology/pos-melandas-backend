<?php

namespace App\Http\Resources\V1\ProductUnit;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseProductUnitResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('name', Schema::TYPE_STRING, 'product unit name'),
            ResourceData::make('price', Schema::TYPE_INTEGER, 100000),
            ResourceData::makeRelationship('colour', ColourResource::class),
            ResourceData::makeRelationship('covering', CoveringResource::class),
        ];
    }
}