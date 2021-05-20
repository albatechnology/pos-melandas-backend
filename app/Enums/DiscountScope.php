<?php

namespace App\Enums;

/**
 * @method static static QUANTITY()
 * @method static static TYPE()
 * @method static static TRANSACTION()
 */
final class DiscountScope extends BaseEnum
{
    const QUANTITY    = 0;
    const TYPE        = 1;
    const TRANSACTION = 2;

    public static function getDescription($value): string
    {
        return match ($value) {
            self::QUANTITY => 'Per Product Unit Quantity',
            self::TYPE => 'Per Product Unit Type',
            self::TRANSACTION => 'Per Transaction',
            default => self::getKey($value),
        };
    }

    public function applyToOrder(): bool
    {
        return match ($this->value) {
            self::TRANSACTION => true,
            default => false,
        };
    }
}