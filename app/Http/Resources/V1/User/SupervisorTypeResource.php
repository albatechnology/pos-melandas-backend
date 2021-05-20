<?php

namespace App\Http\Resources\V1\User;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class SupervisorTypeResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("name", Schema::TYPE_STRING, 'Test image'),
            ResourceData::make("fileName", Schema::TYPE_STRING, 'test.png'),
            ResourceData::make("mimeType", Schema::TYPE_STRING, 'binary/octet-stream'),
            ResourceData::make("size", Schema::TYPE_INTEGER, 100),
            ResourceData::make("url", Schema::TYPE_STRING, 'https://print-trail-media-dev.s3.eu-west-2.amazonaws.com/7533653a-a88c-45b8-89bd-b4e76241d6dc/test.png'),
        ];
    }
}
