@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.paymentType.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.payment-types.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.paymentType.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.paymentType.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="code">{{ trans('cruds.paymentType.fields.code') }}</label>
                <input class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" type="text" name="code" id="code" value="{{ old('code', '') }}">
                @if($errors->has('code'))
                    <span class="text-danger">{{ $errors->first('code') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.paymentType.fields.code_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="payment_category_id">{{ trans('cruds.paymentType.fields.payment_category') }}</label>
                <select class="form-control select2 {{ $errors->has('payment_category') ? 'is-invalid' : '' }}" name="payment_category_id" id="payment_category_id">
                    @foreach($payment_categories as $id => $payment_category)
                        <option value="{{ $id }}" {{ old('payment_category_id') == $id ? 'selected' : '' }}>{{ $payment_category }}</option>
                    @endforeach
                </select>
                @if($errors->has('payment_category'))
                    <span class="text-danger">{{ $errors->first('payment_category') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.paymentType.fields.payment_category_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('require_approval') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="require_approval" value="0">
                    <input class="form-check-input" type="checkbox" name="require_approval" id="require_approval" value="1" {{ old('require_approval', 0) == 1 || old('require_approval') === null ? 'checked' : '' }}>
                    <label class="form-check-label" for="require_approval">{{ trans('cruds.paymentType.fields.require_approval') }}</label>
                </div>
                @if($errors->has('require_approval'))
                    <span class="text-danger">{{ $errors->first('require_approval') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.paymentType.fields.require_approval_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="company_id">{{ trans('cruds.paymentType.fields.company') }}</label>
                <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="company_id" id="company_id" required>
                    @foreach($companies as $id => $company)
                        <option value="{{ $id }}" {{ old('company_id') == $id ? 'selected' : '' }}>{{ $company }}</option>
                    @endforeach
                </select>
                @if($errors->has('company'))
                    <span class="text-danger">{{ $errors->first('company') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.paymentType.fields.company_helper') }}</span>
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