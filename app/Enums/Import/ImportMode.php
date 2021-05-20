<?php

namespace App\Enums\Import;

use App\Enums\BaseEnum;

/**
 * @method static static UPDATE_DUPLICATE()
 * @method static static SKIP_DUPLICATE()
 */
final class ImportMode extends BaseEnum
{
    const UPDATE_DUPLICATE = 0;
    const SKIP_DUPLICATE   = 1;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}