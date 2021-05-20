<?php

namespace App\Pipes\Order;

use App\Enums\OrderDetailStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductUnit;
use Closure;

class MakeOrderLines
{
    public function handle(Order $order, Closure $next)
    {
        // Starts by grabbing all the product unit model
        $items = collect($order->raw_source['items']);
        $units = ProductUnit::whereIn('id', $items->pluck('id'))
                            ->with(['product', 'colour', 'covering'])
                            ->get()
                            ->keyBy('id');

        $order_details = $items->map(function ($data) use ($units) {

            /** @var ProductUnit $product_unit */
            $product_unit = $units[$data['id']];

            $order_detail             = new OrderDetail();
            $order_detail->status     = OrderDetailStatus::NOT_FULFILLED();
            $order_detail->company_id = user()->company_id;

            // We do not bother with stock fulfilment and discount
            // calculation yet at this stage
            $order_detail->records         = [
                'product_unit' => $product_unit->toRecord(),
                'product'      => $product_unit->product->toRecord()
            ];
            $order_detail->quantity        = (int)$data['quantity'];
            $order_detail->product_unit_id = $product_unit->id;
            $order_detail->unit_price      = $product_unit->price;
            $order_detail->total_discount  = 0;
            $order_detail->total_price     = $product_unit->price * $data['quantity'];

            return $order_detail;
        });

        $order->order_details = $order_details;
        //$order->order_details_price = $order_details->sum(fn(OrderDetail $detail) => $detail->total_price);
        $order->total_price = $order_details->sum(fn(OrderDetail $detail) => $detail->total_price);
        //$order->total_price         = $order->order_details_price;

        return $next($order);
    }
}