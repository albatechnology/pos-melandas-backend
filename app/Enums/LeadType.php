<?php

namespace App\Enums;

/**
 * @method static static PROSPECT()
 * @method static static CLOSED()
 * @method static static LEADS()
 */
final class LeadType extends BaseEnum
{
    const PROSPECT = 1;
    const CLOSED   = 2;
    const LEADS    = 3;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}