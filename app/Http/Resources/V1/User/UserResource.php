<?php

namespace App\Http\Resources\V1\User;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\UserType;
use App\Http\Resources\V1\Company\CompanyResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UserResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("name", Schema::TYPE_STRING, 'Admin')->sortable(),
            ResourceData::make("email", Schema::TYPE_STRING, ApiDataExample::EMAIL)->sortable(),
            ResourceData::make("email_verified_at", Schema::TYPE_STRING, ApiDataExample::TIMESTAMP)->notNested(),
            ResourceData::makeEnum('type', UserType::class)
                        ->description("User type determine the feature should be made avaiable to them. The available enum options are hard coded."),

            ResourceData::makeRelationship('company', CompanyResource::class),
            ResourceData::make("company_id", Schema::TYPE_INTEGER, 1)
                        ->description("The company that this user belongs to")
                        ->nullable()
                        ->sortable(),

            ResourceData::make("channel_id", Schema::TYPE_INTEGER, 1)
                        ->description("The default channel currently selected for this user")
                        ->nullable()
                        ->sortable(),

            ResourceData::make("supervisor_id", Schema::TYPE_INTEGER, 1)
                        ->description("The user id of this user's supervisor")
                        ->nullable(),

            ResourceData::make("supervisor_type", Schema::TYPE_STRING, "Store Leader")
                        ->value(fn($v) => $v->supervisorType->name ?? null)
                        ->description("The supervisor type of this user if this user is a supervisor. This value is data driven.")
                        ->nullable()
                        ->notNested(),

            // uncomment this to show an array of channel ids available to this user
            //
            //            ResourceData::make("channel_ids", Schema::TYPE_ARRAY, null,
            //                null, fn($data) => $data->channelsPivot ? $data->channelsPivot->pluck('channel_id') : []
            //            )->nullable(),
            //            ResourceData::make("channel_ids.*", Schema::TYPE_INTEGER, 1)
            //                ->parent("channel_ids"),


        ];
    }
}
