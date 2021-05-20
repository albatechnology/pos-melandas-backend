<?php

namespace App\Http\Resources\V1\Lead;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Http\Resources\V1\Channel\ChannelResource;
use App\Http\Resources\V1\Customer\CustomerResource;
use App\Http\Resources\V1\User\UserResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class LeadResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::makeEnum("type", LeadType::class),
            ResourceData::makeEnum("status", LeadStatus::class),
            ResourceData::make('label', Schema::TYPE_STRING, 'my prospect')
                        ->description('User provided/auto generated description for the lead.'),

            ResourceData::makeRelationship('customer', CustomerResource::class),
            ResourceData::makeRelationship('user', UserResource::class),
            ResourceData::makeRelationship('channel', ChannelResource::class),
            ...ResourceData::timestamps()
        ];
    }
}