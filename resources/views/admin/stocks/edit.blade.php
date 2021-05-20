@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.stock.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.stocks.update", [$stock->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="stock">{{ trans('cruds.stock.fields.current_stock') }}</label>
                <input class="form-control {{ $errors->has('stock') ? 'is-invalid' : '' }}" type="number" disabled
                       id="stock" value="{{ $stock->stock }}" step="1">
                <span class="help-block">{{ trans('cruds.stock.fields.stock_helper') }}</span>
            </div>
            <x-input key="increment"
                     value="0"
                     label-key="cruds.stock.fields.add_stock"
                     type="number">
            </x-input>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection