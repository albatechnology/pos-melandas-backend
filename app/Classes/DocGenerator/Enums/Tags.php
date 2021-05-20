<?php

namespace App\Classes\DocGenerator\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static V1()
 * @method static static Dummy()
 * @method static static User()
 * @method static static Auth()
 * @method static static Company()
 * @method static static Channel()
 * @method static static Product()
 * @method static static ProductUnit()
 * @method static static Address()
 * @method static static Lead()
 * @method static static Activity()
 * @method static static ActivityComment()
 * @method static static ProductTag()
 * @method static static ProductCategory()
 * @method static static QaTopic()
 * @method static static QaMessage()
 * @method static static Cart()
 * @method static static Discount()
 * @method static static Order()
 */
final class Tags extends Enum
{
    const Dummy           = "Dummy";
    const V1              = "V1";
    const User            = "User";
    const Auth            = "Auth";
    const Company         = "Company";
    const Channel         = "Channel";
    const Product         = "Product";
    const ProductUnit     = "ProductUnit";
    const Address         = "Address";
    const Customer        = "Customer";
    const Lead            = "Lead";
    const Activity        = "Activity";
    const Rule            = "Rule";
    const ActivityComment = "ActivityComment";
    const ProductTag      = "ProductTag";
    const ProductCategory = "ProductCategory";
    const QaTopic         = "QaTopic";
    const QaMessage       = "QaMessage";
    const Cart            = "Cart";
    const Discount        = "Discount";
    const Order           = "Order";

    public static function getDescription($value): string
    {
        return match ($value) {
            self::Dummy => 'Unfinished API that returns static value rather than data driven results',
            self::V1 => 'All version 1 API',
            //self::V2 => 'All version 2 API',
            self::User => 'Application users',
            self::Auth => 'Application auth',
            self::Company => 'Application company',
            self::Channel => 'Application channel',
            self::Product => 'Application product',
            self::Address => 'Customer address',
            self::Rule => 'Endpoint that provides validation rule for another endpoint',
            default => self::getKey($value),
        };
    }

    public static function openApiTags(): array
    {
        return collect(self::getInstances())
            ->map(fn($d) => ["name" => $d->value, "description" => $d->description])
            ->values()
            ->toArray();
    }
}
