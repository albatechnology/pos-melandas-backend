<?php

namespace App\Pipes\Order;

use App\Models\Discount;
use App\Models\Order;
use App\Services\OrderService;
use Closure;

/**
 * Apply discount on order and order detail level.
 *
 * Class ApplyDiscount
 * @package App\Pipes\Order
 */
class ApplyDiscount
{
    public function handle(Order $order, Closure $next)
    {
        if (!$discount_id = $order->raw_source['discount_id'] ?? null) return $next($order);

        $discount = Discount::findOrFail($discount_id);
        OrderService::setDiscount($order, $discount);

        if ($discount = $order->getDiscount()) {
            $records             = $order->records;
            $records['discount'] = $discount->toRecord();
            $order->records      = $records;
        }

        return $next($order);
    }
}