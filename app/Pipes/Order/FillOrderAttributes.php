<?php

namespace App\Pipes\Order;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Models\Lead;
use App\Models\Order;
use Closure;
use Exception;

/**
 * Fill order attributes from raw source
 *
 * Class FillOrderAttributes
 * @package App\Pipes\Order
 */
class FillOrderAttributes
{
    /**
     * @param Order $order
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Order $order, Closure $next): mixed
    {
        if (empty($order->raw_source)) throw new Exception('Order raw source is not initiated!');

        $order->note           = $order->raw_source['note'] ?? null;
        $order->lead_id        = $order->raw_source['lead_id'];
        $order->shipping_fee   = $order->raw_source['shipping_fee'] ?? 0;
        $order->packing_fee    = $order->raw_source['packing_fee'] ?? 0;
        $order->user_id        = user()->id;
        $order->customer_id    = Lead::findOrFail($order->raw_source['lead_id'])->customer_id;
        $order->channel_id     = user()->channel_id;
        $order->company_id     = user()->company_id;
        $order->total_discount = 0;
        $order->status         = OrderStatus::QUOTATION();
        $order->payment_status = OrderPaymentStatus::NONE();
        $order->expected_price = $order->raw_source['expected_price'] ?? null;

        return $next($order);
    }
}