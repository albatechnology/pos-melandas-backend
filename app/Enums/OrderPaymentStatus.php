<?php

namespace App\Enums;

/**
 * @method static static NONE()
 * @method static static DP()
 * @method static static PARTIAL()
 * @method static static SETTLEMENT()
 * @method static static OVERPAYMENT()
 * @method static static REFUNDED()
 */
final class OrderPaymentStatus extends BaseEnum
{
    const NONE        = 1;
    const DP          = 2;
    const PARTIAL     = 3;
    const SETTLEMENT  = 4;
    const OVERPAYMENT = 5;
    const REFUNDED    = 6;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}