<?php

namespace App\Enums;

/**
 * @method static static PHONE()
 * @method static static WHATSAPP()
 * @method static static MEETING()
 * @method static static OTHERS()
 */
final class ActivityFollowUpMethod extends BaseEnum
{
    const PHONE    = 1;
    const WHATSAPP = 2;
    const MEETING  = 3;
    const OTHERS   = 4;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}