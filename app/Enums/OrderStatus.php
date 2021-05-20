<?php

namespace App\Enums;

/**
 * @method static static QUOTATION()
 * @method static static DP()
 * @method static static PAID()
 * @method static static DELIVERING()
 * @method static static ARRIVED()
 * @method static static CANCELLED()
 */
final class OrderStatus extends BaseEnum
{
    const QUOTATION  = 1;
    const WAREHOUSE  = 2;
    const DELIVERING = 3;
    const ARRIVED    = 4;
    const CANCELLED  = 5;
    const RETURNED   = 6;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}