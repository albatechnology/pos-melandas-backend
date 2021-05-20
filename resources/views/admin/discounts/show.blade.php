@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.discount.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.discounts.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.id') }}
                        </th>
                        <td>
                            {{ $discount->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.description') }}
                        </th>
                        <td>
                            {{ $discount->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\Discount::TYPE_SELECT[$discount->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.activation_code') }}
                        </th>
                        <td>
                            {{ $discount->activation_code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.value') }}
                        </th>
                        <td>
                            {{ $discount->value }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.scope') }}
                        </th>
                        <td>
                            {{ App\Models\Discount::SCOPE_SELECT[$discount->scope] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.start_time') }}
                        </th>
                        <td>
                            {{ $discount->start_time }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.end_time') }}
                        </th>
                        <td>
                            {{ $discount->end_time }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.is_active') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $discount->is_active ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.max_discount_price_per_order') }}
                        </th>
                        <td>
                            {{ $discount->max_discount_price_per_order }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.max_use_per_customer') }}
                        </th>
                        <td>
                            {{ $discount->max_use_per_customer }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.min_order_price') }}
                        </th>
                        <td>
                            {{ $discount->min_order_price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.discount.fields.company') }}
                        </th>
                        <td>
                            {{ $discount->company->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.discounts.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection