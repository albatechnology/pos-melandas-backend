<?php

namespace App\Pipes\Discountable;

use App\Interfaces\Discountable;
use App\Interfaces\DiscountableLine;
use App\Services\OrderService;
use Closure;
use Exception;

/**
 * Apply discount to the discountable lines class if applicable
 * Class ResetDiscount
 * @package App\Pipes\Discountable
 */
class CalculateDiscountForDiscountableLineClass
{
    /**
     * @param Discountable $discountable
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Discountable $discountable, Closure $next)
    {
        if (!$discount = $discountable->getDiscount()) return $next($discountable);

        if ($discount->applyToProductUnit()) {
            $discountable->getDiscountableLines()->each(function (DiscountableLine $line) use ($discount) {
                $line->setTotalDiscount(OrderService::calculateTotalDiscount($line, $discount));
                $line->setTotalPrice($line->getTotalPrice() - $line->getTotalDiscount());
            });

            $discountable->updatePricesFromItemLine();
        }

        return $next($discountable);
    }
}