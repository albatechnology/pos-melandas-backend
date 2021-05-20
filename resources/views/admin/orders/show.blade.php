@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.order.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.orders.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.id') }}
                        </th>
                        <td>
                            {{ $order->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.user') }}
                        </th>
                        <td>
                            {{ $order->user->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.customer') }}
                        </th>
                        <td>
                            {{ $order->customer->first_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.address') }}
                        </th>
                        <td>
                            {{ $order->address->address_line_1 ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.channel') }}
                        </th>
                        <td>
                            {{ $order->channel->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.reference') }}
                        </th>
                        <td>
                            {{ $order->reference }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.delivery_address') }}
                        </th>
                        <td>
                            {{ $order->delivery_address }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.status') }}
                        </th>
                        <td>
                            {{ $order->status->description ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.price') }}
                        </th>
                        <td>
                            {{ $order->price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.mutations') }}
                        </th>
                        <td>
                            {{ $order->mutations }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.tax_invoice_sent') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $order->tax_invoice_sent ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.tax_invoice') }}
                        </th>
                        <td>
                            {{ $order->tax_invoice->company_name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.orders.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#order_order_trackings" role="tab" data-toggle="tab">
                {{ trans('cruds.orderTracking.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#order_order_details" role="tab" data-toggle="tab">
                {{ trans('cruds.orderDetail.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#order_shipments" role="tab" data-toggle="tab">
                {{ trans('cruds.shipment.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#order_payments" role="tab" data-toggle="tab">
                {{ trans('cruds.payment.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#orders_targets" role="tab" data-toggle="tab">
                {{ trans('cruds.target.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="order_order_trackings">
            @includeIf('admin.orders.relationships.orderOrderTrackings', ['orderTrackings' => $order->orderOrderTrackings])
        </div>
        <div class="tab-pane" role="tabpanel" id="order_order_details">
            @includeIf('admin.orders.relationships.orderOrderDetails', ['orderDetails' => $order->orderOrderDetails])
        </div>
        <div class="tab-pane" role="tabpanel" id="order_shipments">
            @includeIf('admin.orders.relationships.orderShipments', ['shipments' => $order->orderShipments])
        </div>
        <div class="tab-pane" role="tabpanel" id="order_payments">
            @includeIf('admin.orders.relationships.orderPayments', ['payments' => $order->orderPayments])
        </div>
        <div class="tab-pane" role="tabpanel" id="orders_targets">
            @includeIf('admin.orders.relationships.ordersTargets', ['targets' => $order->ordersTargets])
        </div>
    </div>
</div>

@endsection