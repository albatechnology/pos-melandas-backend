@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.shipment.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.shipments.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="order_id">{{ trans('cruds.shipment.fields.order') }}</label>
                <select class="form-control select2 {{ $errors->has('order') ? 'is-invalid' : '' }}" name="order_id" id="order_id" required>
                    @foreach($orders as $id => $order)
                        <option value="{{ $id }}" {{ old('order_id') == $id ? 'selected' : '' }}>{{ $order }}</option>
                    @endforeach
                </select>
                @if($errors->has('order'))
                    <span class="text-danger">{{ $errors->first('order') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.shipment.fields.order_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.shipment.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Shipment::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', 'waiting') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.shipment.fields.status_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="note">{{ trans('cruds.shipment.fields.note') }}</label>
                <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note') }}</textarea>
                @if($errors->has('note'))
                    <span class="text-danger">{{ $errors->first('note') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.shipment.fields.note_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="reference">{{ trans('cruds.shipment.fields.reference') }}</label>
                <input class="form-control {{ $errors->has('reference') ? 'is-invalid' : '' }}" type="text" name="reference" id="reference" value="{{ old('reference', '') }}">
                @if($errors->has('reference'))
                    <span class="text-danger">{{ $errors->first('reference') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.shipment.fields.reference_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="estimated_delivery_date">{{ trans('cruds.shipment.fields.estimated_delivery_date') }}</label>
                <input class="form-control date {{ $errors->has('estimated_delivery_date') ? 'is-invalid' : '' }}" type="text" name="estimated_delivery_date" id="estimated_delivery_date" value="{{ old('estimated_delivery_date') }}">
                @if($errors->has('estimated_delivery_date'))
                    <span class="text-danger">{{ $errors->first('estimated_delivery_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.shipment.fields.estimated_delivery_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="arrived_at">{{ trans('cruds.shipment.fields.arrived_at') }}</label>
                <input class="form-control datetime {{ $errors->has('arrived_at') ? 'is-invalid' : '' }}" type="text" name="arrived_at" id="arrived_at" value="{{ old('arrived_at') }}">
                @if($errors->has('arrived_at'))
                    <span class="text-danger">{{ $errors->first('arrived_at') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.shipment.fields.arrived_at_helper') }}</span>
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