<?php

namespace App\Http\Resources\V1\Activity;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\ActivityFollowUpMethod;
use App\Enums\ActivityStatus;
use App\Http\Resources\V1\Channel\ChannelResource;
use App\Http\Resources\V1\Customer\CustomerResource;
use App\Http\Resources\V1\Lead\LeadResource;
use App\Http\Resources\V1\User\UserResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ActivityResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make('follow_up_datetime', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP),
            ResourceData::make('feedback', Schema::TYPE_STRING, 'Customer feedback'),
            ResourceData::makeEnum("follow_up_method", ActivityFollowUpMethod::class),
            ResourceData::makeEnum("status", ActivityStatus::class),
            ResourceData::makeRelationship('channel', ChannelResource::class, null, fn($r) => cache_service()->channels($r->channel_id)),
            ResourceData::make('order_id', Schema::TYPE_INTEGER, 1)->nullable(),
            //ResourceData::makeRelationship('company', CompanyResource::class, null, fn($r) => cache_service()->companyOfChannel($r->channel_id)),
            ResourceData::makeRelationship('lead', LeadResource::class),
            ResourceData::makeRelationship('user', UserResource::class),
            ResourceData::makeRelationship('customer', CustomerResource::class),
            ResourceData::makeRelationship('latest_comment', ActivityCommentResource::class, 'latestComment'),
            ResourceData::make('activity_comment_count', Schema::TYPE_INTEGER, 1),
            ResourceData::make('reminder_datetime', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP)->nullable(),
            ResourceData::make('reminder_note', Schema::TYPE_STRING, 'Remind myself to follow up')->nullable(),
            ...ResourceData::timestamps()
        ];
    }
}