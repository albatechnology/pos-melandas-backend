@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.order.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.orders.update", [$order->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="address_id">{{ trans('cruds.order.fields.address') }}</label>
                <select class="form-control select2 {{ $errors->has('address') ? 'is-invalid' : '' }}" name="address_id" id="address_id" required>
                    @foreach($addresses as $id => $address)
                        <option value="{{ $id }}" {{ (old('address_id') ? old('address_id') : $order->address->id ?? '') == $id ? 'selected' : '' }}>{{ $address }}</option>
                    @endforeach
                </select>
                @if($errors->has('address'))
                    <span class="text-danger">{{ $errors->first('address') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.address_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="channel_id">{{ trans('cruds.order.fields.channel') }}</label>
                <select class="form-control select2 {{ $errors->has('channel') ? 'is-invalid' : '' }}" name="channel_id" id="channel_id" required>
                    @foreach($channels as $id => $channel)
                        <option value="{{ $id }}" {{ (old('channel_id') ? old('channel_id') : $order->channel->id ?? '') == $id ? 'selected' : '' }}>{{ $channel }}</option>
                    @endforeach
                </select>
                @if($errors->has('channel'))
                    <span class="text-danger">{{ $errors->first('channel') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.channel_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="delivery_address">{{ trans('cruds.order.fields.delivery_address') }}</label>
                <textarea class="form-control {{ $errors->has('delivery_address') ? 'is-invalid' : '' }}" name="delivery_address" id="delivery_address">{{ old('delivery_address', $order->delivery_address) }}</textarea>
                @if($errors->has('delivery_address'))
                    <span class="text-danger">{{ $errors->first('delivery_address') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.delivery_address_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('tax_invoice_sent') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="tax_invoice_sent" value="0">
                    <input class="form-check-input" type="checkbox" name="tax_invoice_sent" id="tax_invoice_sent" value="1" {{ $order->tax_invoice_sent || old('tax_invoice_sent', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="tax_invoice_sent">{{ trans('cruds.order.fields.tax_invoice_sent') }}</label>
                </div>
                @if($errors->has('tax_invoice_sent'))
                    <span class="text-danger">{{ $errors->first('tax_invoice_sent') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.tax_invoice_sent_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="tax_invoice_id">{{ trans('cruds.order.fields.tax_invoice') }}</label>
                <select class="form-control select2 {{ $errors->has('tax_invoice') ? 'is-invalid' : '' }}" name="tax_invoice_id" id="tax_invoice_id">
                    @foreach($tax_invoices as $id => $tax_invoice)
                        <option value="{{ $id }}" {{ (old('tax_invoice_id') ? old('tax_invoice_id') : $order->tax_invoice->id ?? '') == $id ? 'selected' : '' }}>{{ $tax_invoice }}</option>
                    @endforeach
                </select>
                @if($errors->has('tax_invoice'))
                    <span class="text-danger">{{ $errors->first('tax_invoice') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.tax_invoice_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection