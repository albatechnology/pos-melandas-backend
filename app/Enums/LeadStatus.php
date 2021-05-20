<?php

namespace App\Enums;

use Exception;

/**
 * @method static static RED()
 * @method static static YELLOW()
 * @method static static GREEN()
 * @method static static EXPIRED()
 * @method static static SALES()
 * @method static static OTHER_SALES()
 */
final class LeadStatus extends BaseEnum
{
    const GREEN       = 1;
    const YELLOW      = 2;
    const RED         = 3;
    const EXPIRED     = 4;
    const SALES       = 5;
    const OTHER_SALES = 6;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }

    /**
     * The next status progression from a given status
     *
     * @return $this
     */
    public function nextStatus(): ?self
    {
        return match ($this->value) {
            self::GREEN => self::YELLOW(),
            self::YELLOW => self::RED(),
            self::RED => self::EXPIRED(),
            default => null,
        };
    }

    /**
     * The delay duration for this status before
     * it should be moved to the next status
     * @throws Exception
     */
    public function delayDuration(): int
    {
        if (!$next_status = $this->nextStatus()) return 0;

        $key = 'core.lead_status_duration_seconds.' . $this->value;

        return config($key) ?? throw new Exception($key . ' key is missing from config file!');
    }
}