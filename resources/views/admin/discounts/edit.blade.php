@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.discount.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.discounts.update", [$discount->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="description">{{ trans('cruds.discount.fields.description') }}</label>
                <input class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" type="text" name="description" id="description" value="{{ old('description', $discount->description) }}">
                @if($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.discount.fields.description_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.discount.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Discount::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $discount->type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.discount.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="activation_code">{{ trans('cruds.discount.fields.activation_code') }}</label>
                <input class="form-control {{ $errors->has('activation_code') ? 'is-invalid' : '' }}" type="text" name="activation_code" id="activation_code" value="{{ old('activation_code', $discount->activation_code) }}">
                @if($errors->has('activation_code'))
                    <span class="text-danger">{{ $errors->first('activation_code') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.discount.fields.activation_code_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="value">{{ trans('cruds.discount.fields.value') }}</label>
                <input class="form-control {{ $errors->has('value') ? 'is-invalid' : '' }}" type="number" name="value" id="value" value="{{ old('value', $discount->value) }}" step="1">
                @if($errors->has('value'))
                    <span class="text-danger">{{ $errors->first('value') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.discount.fields.value_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.discount.fields.scope') }}</label>
                <select class="form-control {{ $errors->has('scope') ? 'is-invalid' : '' }}" name="scope" id="scope">
                    <option value disabled {{ old('scope', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Discount::SCOPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('scope', $discount->scope) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('scope'))
                    <span class="text-danger">{{ $errors->first('scope') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.discount.fields.scope_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="start_time">{{ trans('cruds.discount.fields.start_time') }}</label>
                <input class="form-control datetime {{ $errors->has('start_time') ? 'is-invalid' : '' }}" type="text" name="start_time" id="start_time" value="{{ old('start_time', $discount->start_time) }}" required>
                @if($errors->has('start_time'))
                    <span class="text-danger">{{ $errors->first('start_time') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.discount.fields.start_time_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="end_time">{{ trans('cruds.discount.fields.end_time') }}</label>
                <input class="form-control datetime {{ $errors->has('end_time') ? 'is-invalid' : '' }}" type="text" name="end_time" id="end_time" value="{{ old('end_time', $discount->end_time) }}" required>
                @if($errors->has('end_time'))
                    <span class="text-danger">{{ $errors->first('end_time') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.discount.fields.end_time_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('is_active') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $discount->is_active || old('is_active', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">{{ trans('cruds.discount.fields.is_active') }}</label>
                </div>
                @if($errors->has('is_active'))
                    <span class="text-danger">{{ $errors->first('is_active') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.discount.fields.is_active_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="max_discount_price_per_order">{{ trans('cruds.discount.fields.max_discount_price_per_order') }}</label>
                <input class="form-control {{ $errors->has('max_discount_price_per_order') ? 'is-invalid' : '' }}" type="number" name="max_discount_price_per_order" id="max_discount_price_per_order" value="{{ old('max_discount_price_per_order', $discount->max_discount_price_per_order) }}" step="0.01">
                @if($errors->has('max_discount_price_per_order'))
                    <span class="text-danger">{{ $errors->first('max_discount_price_per_order') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.discount.fields.max_discount_price_per_order_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="max_use_per_customer">{{ trans('cruds.discount.fields.max_use_per_customer') }}</label>
                <input class="form-control {{ $errors->has('max_use_per_customer') ? 'is-invalid' : '' }}" type="number" name="max_use_per_customer" id="max_use_per_customer" value="{{ old('max_use_per_customer', $discount->max_use_per_customer) }}" step="1">
                @if($errors->has('max_use_per_customer'))
                    <span class="text-danger">{{ $errors->first('max_use_per_customer') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.discount.fields.max_use_per_customer_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="min_order_price">{{ trans('cruds.discount.fields.min_order_price') }}</label>
                <input class="form-control {{ $errors->has('min_order_price') ? 'is-invalid' : '' }}" type="number" name="min_order_price" id="min_order_price" value="{{ old('min_order_price', $discount->min_order_price) }}" step="0.01">
                @if($errors->has('min_order_price'))
                    <span class="text-danger">{{ $errors->first('min_order_price') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.discount.fields.min_order_price_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="company_id">{{ trans('cruds.discount.fields.company') }}</label>
                <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="company_id" id="company_id" required>
                    @foreach($companies as $id => $company)
                        <option value="{{ $id }}" {{ (old('company_id') ? old('company_id') : $discount->company->id ?? '') == $id ? 'selected' : '' }}>{{ $company }}</option>
                    @endforeach
                </select>
                @if($errors->has('company'))
                    <span class="text-danger">{{ $errors->first('company') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.discount.fields.company_helper') }}</span>
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