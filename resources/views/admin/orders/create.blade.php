@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.order.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.orders.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="user_id">{{ trans('cruds.order.fields.user') }}</label>
                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id">
                    @foreach($users as $id => $user)
                        <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $user }}</option>
                    @endforeach
                </select>
                @if($errors->has('user'))
                    <span class="text-danger">{{ $errors->first('user') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.user_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="customer_id">{{ trans('cruds.order.fields.customer') }}</label>
                <select class="form-control select2 {{ $errors->has('customer') ? 'is-invalid' : '' }}" name="customer_id" id="customer_id" required>
                    @foreach($customers as $id => $customer)
                        <option value="{{ $id }}" {{ old('customer_id') == $id ? 'selected' : '' }}>{{ $customer }}</option>
                    @endforeach
                </select>
                @if($errors->has('customer'))
                    <span class="text-danger">{{ $errors->first('customer') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.customer_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="address_id">{{ trans('cruds.order.fields.address') }}</label>
                <select class="form-control select2 {{ $errors->has('address') ? 'is-invalid' : '' }}" name="address_id" id="address_id" required>
                    @foreach($addresses as $id => $address)
                        <option value="{{ $id }}" {{ old('address_id') == $id ? 'selected' : '' }}>{{ $address }}</option>
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
                        <option value="{{ $id }}" {{ old('channel_id') == $id ? 'selected' : '' }}>{{ $channel }}</option>
                    @endforeach
                </select>
                @if($errors->has('channel'))
                    <span class="text-danger">{{ $errors->first('channel') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.channel_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="reference">{{ trans('cruds.order.fields.reference') }}</label>
                <input class="form-control {{ $errors->has('reference') ? 'is-invalid' : '' }}" type="text" name="reference" id="reference" value="{{ old('reference', '') }}" required>
                @if($errors->has('reference'))
                    <span class="text-danger">{{ $errors->first('reference') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.reference_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="delivery_address">{{ trans('cruds.order.fields.delivery_address') }}</label>
                <textarea class="form-control {{ $errors->has('delivery_address') ? 'is-invalid' : '' }}"
                          name="delivery_address" id="delivery_address">{{ old('delivery_address') }}</textarea>
                @if($errors->has('delivery_address'))
                    <span class="text-danger">{{ $errors->first('delivery_address') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.delivery_address_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.order.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status"
                        required>
                    <option value
                            disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(\App\Enums\OrderStatus::getInstances() as $enum)
                        <option value="{{ $enum->value }}" {{ old('status', 'new') === (string) $enum->value ? 'selected' : '' }}>{{ $enum->description }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.status_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="price">{{ trans('cruds.order.fields.price') }}</label>
                <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="number" name="price"
                       id="price" value="{{ old('price', '') }}" step="0.01" required>
                @if($errors->has('price'))
                    <span class="text-danger">{{ $errors->first('price') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.price_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="mutations">{{ trans('cruds.order.fields.mutations') }}</label>
                <textarea class="form-control {{ $errors->has('mutations') ? 'is-invalid' : '' }}" name="mutations" id="mutations">{{ old('mutations') }}</textarea>
                @if($errors->has('mutations'))
                    <span class="text-danger">{{ $errors->first('mutations') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.mutations_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('tax_invoice_sent') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="tax_invoice_sent" value="0">
                    <input class="form-check-input" type="checkbox" name="tax_invoice_sent" id="tax_invoice_sent" value="1" {{ old('tax_invoice_sent', 0) == 1 ? 'checked' : '' }}>
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
                        <option value="{{ $id }}" {{ old('tax_invoice_id') == $id ? 'selected' : '' }}>{{ $tax_invoice }}</option>
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