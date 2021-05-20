@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.orderDetail.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.order-details.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.orderDetail.fields.id') }}
                        </th>
                        <td>
                            {{ $orderDetail->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderDetail.fields.product_unit') }}
                        </th>
                        <td>
                            {{ $orderDetail->product_unit->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderDetail.fields.order') }}
                        </th>
                        <td>
                            {{ $orderDetail->order->reference ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderDetail.fields.product_detail') }}
                        </th>
                        <td>
                            {{ $orderDetail->product_detail }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderDetail.fields.note') }}
                        </th>
                        <td>
                            {!! $orderDetail->note !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderDetail.fields.quantity') }}
                        </th>
                        <td>
                            {{ $orderDetail->quantity }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderDetail.fields.mutations') }}
                        </th>
                        <td>
                            {{ $orderDetail->mutations }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderDetail.fields.unit_price') }}
                        </th>
                        <td>
                            {{ $orderDetail->unit_price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderDetail.fields.price') }}
                        </th>
                        <td>
                            {{ $orderDetail->price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderDetail.fields.notes') }}
                        </th>
                        <td>
                            {!! $orderDetail->notes !!}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.order-details.index') }}">
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
            <a class="nav-link" href="#order_details_targets" role="tab" data-toggle="tab">
                {{ trans('cruds.target.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="order_details_targets">
            @includeIf('admin.orderDetails.relationships.orderDetailsTargets', ['targets' => $orderDetail->orderDetailsTargets])
        </div>
    </div>
</div>

@endsection