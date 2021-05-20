<?php

namespace App\Pipes\Order;

use App\Models\Order;
use Closure;

/**
 * Class AddAdditionalFees
 * @package App\Pipes\Order
 */
class AddAdditionalFees
{
    public function handle(Order $order, Closure $next)
    {
        if ($order->shipping_fee) {
            $order->total_price += $order->shipping_fee;
        }

        if ($order->packing_fee) {
            $order->total_price += $order->packing_fee;
        }

        return $next($order);
    }
}