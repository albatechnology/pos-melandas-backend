<?php

namespace App\Http\Resources\V1\Product;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseProductResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("name", Schema::TYPE_STRING, 'Product ABC'),
            ResourceData::make("price", Schema::TYPE_INTEGER, 100000),
        ];
    }
//
//    public function toArray($request){
//        return $this->resource;
//    }
}
