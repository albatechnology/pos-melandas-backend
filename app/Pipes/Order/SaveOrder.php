<?php

namespace App\Pipes\Order;

use App\Models\Order;
use App\Models\OrderDetail;
use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Save order, order detail, and activity
 *
 * Class SaveOrder
 * @package App\Pipes\Order
 */
class SaveOrder
{
    public function handle(Order $order, Closure $next)
    {
        $order = DB::transaction(function () use ($order) {
            $details = $order->order_details;
            unset($order->order_details);
            unset($order->discount);

            collect($details)->each(function (OrderDetail $detail) {
                unset($detail->discount);
                unset($detail->discount_id);
            });

            $order->save();
            $order->order_details()->saveMany($details);

            return $order;
        });

        return $next($order);
    }
}