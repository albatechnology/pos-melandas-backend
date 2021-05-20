<?php

namespace App\Enums;

/**
 * @method static static NOT_FULFILLED()
 * @method static static PARTIALLY_FULFILLED()
 * @method static static FULFILLED()
 */
final class OrderDetailStatus extends BaseEnum
{
    const NOT_FULFILLED       = 1;
    const PARTIALLY_FULFILLED = 2;
    const FULFILLED           = 3;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}