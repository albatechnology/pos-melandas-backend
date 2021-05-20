<?php

namespace App\Pipes\Order;

use App\Models\Activity;
use App\Models\Order;
use Closure;

/**
 * Create activity for  given order
 *
 * Class MakeActivity
 * @package App\Pipes\Order
 */
class CreateActivity
{
    public function handle(Order $order, Closure $next)
    {
        Activity::createForOrder($order);

        return $next($order);
    }
}