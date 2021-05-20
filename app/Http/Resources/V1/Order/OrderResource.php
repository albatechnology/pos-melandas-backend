<?php

namespace App\Http\Resources\V1\Order;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\DiscountError;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Http\Resources\V1\Address\BaseAddressResource;
use App\Http\Resources\V1\Customer\CustomerResource;
use App\Http\Resources\V1\Discount\BaseDiscountResource;
use App\Http\Resources\V1\OrderDetailResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Http\Resources\MissingValue;

class OrderResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1)->nullable(),

            ResourceData::make('original_price', Schema::TYPE_INTEGER, 1),
            ResourceData::make('total_discount', Schema::TYPE_INTEGER, 1),
            ResourceData::make('total_price', Schema::TYPE_INTEGER, 1),
            ResourceData::make('shipping_fee', Schema::TYPE_INTEGER, 0),
            ResourceData::make('packing_fee', Schema::TYPE_INTEGER, 0),

            ResourceData::make('invoice_number', Schema::TYPE_STRING, 'INV2010123100001')->nullable(),

            ResourceData::makeEnum('status', OrderStatus::class),
            ResourceData::makeEnum('payment_status', OrderPaymentStatus::class),
            ResourceData::makeEnum('discount_error', DiscountError::class, true),

            ResourceData::make('lead_id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('user_id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('channel_id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('discount_id', Schema::TYPE_INTEGER, 1)->nullable(),

            //ResourceData::makeRelationship('activity', ActivityResource::class),

            //ResourceData::make('customer_id', Schema::TYPE_INTEGER, 1),
            ResourceData::makeRelationship('customer', CustomerResource::class),
            ResourceData::makeRelationshipCollection('order_details', OrderDetailResource::class),

            // records
            ResourceData::makeRelationship('billing_address', BaseAddressResource::class, null, fn($q) => $q->records['billing_address']),
            ResourceData::makeRelationship('shipping_address', BaseAddressResource::class, null, fn($q) => $q->records['shipping_address']),
            ResourceData::makeRelationship('discount', BaseDiscountResource::class, null, fn($q) => $q->records['discount'] ?? new MissingValue()),
            // TODO: TAX invoice resource

            ResourceData::make('note', Schema::TYPE_STRING, 'order note')->nullable(),
        ];
    }

    public static function filterIdTimestamp($resource_data)
    {
        return collect($resource_data)->filter(function (ResourceData $data) {
            return !in_array($data->getKey(), ['id', 'updated_at', 'created_at', 'deleted_at']);
        })->all();
    }
}