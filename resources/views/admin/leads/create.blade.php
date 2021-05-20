@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.lead.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.leads.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required">{{ trans('cruds.lead.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Enums\LeadType::getInstances() as $enum)
                        <option value="{{ $enum->value }}" {{ old('type', App\Enums\LeadType::getDefaultValue()) === (string) $enum->value ? 'selected' : '' }}>{{ $enum->label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.lead.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.lead.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Enums\LeadStatus::getInstances() as $enum)
                        <option value="{{ $enum->value }}" {{ old('status', App\Enums\LeadStatus::getDefaultValue()) === (string) $enum->value ? 'selected' : '' }}>{{ $enum->label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.lead.fields.status_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('is_new_customer') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="is_new_customer" value="0">
                    <input class="form-check-input" type="checkbox" name="is_new_customer" id="is_new_customer" value="1" {{ old('is_new_customer', 0) == 1 || old('is_new_customer') === null ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_new_customer">{{ trans('cruds.lead.fields.is_new_customer') }}</label>
                </div>
                @if($errors->has('is_new_customer'))
                    <span class="text-danger">{{ $errors->first('is_new_customer') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.lead.fields.is_new_customer_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="label">{{ trans('cruds.lead.fields.label') }}</label>
                <input class="form-control {{ $errors->has('label') ? 'is-invalid' : '' }}" type="text" name="label" id="label" value="{{ old('label', '') }}">
                @if($errors->has('label'))
                    <span class="text-danger">{{ $errors->first('label') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.lead.fields.label_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="customer_id">{{ trans('cruds.lead.fields.customer') }}</label>
                <select class="form-control select2 {{ $errors->has('customer') ? 'is-invalid' : '' }}" name="customer_id" id="customer_id">
                    @foreach($customers as $id => $customer)
                        <option value="{{ $id }}" {{ old('customer_id') == $id ? 'selected' : '' }}>{{ $customer }}</option>
                    @endforeach
                </select>
                @if($errors->has('customer'))
                    <span class="text-danger">{{ $errors->first('customer') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.lead.fields.customer_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="channel_id">{{ trans('cruds.lead.fields.channel') }}</label>
                <select class="form-control select2 {{ $errors->has('channel') ? 'is-invalid' : '' }}" name="channel_id" id="channel_id" required>
                    @foreach($channels as $id => $channel)
                        <option value="{{ $id }}" {{ old('channel_id') == $id ? 'selected' : '' }}>{{ $channel }}</option>
                    @endforeach
                </select>
                @if($errors->has('channel'))
                    <span class="text-danger">{{ $errors->first('channel') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.lead.fields.channel_helper') }}</span>
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