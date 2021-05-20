@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.colour.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.colours.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.colour.fields.id') }}
                        </th>
                        <td>
                            {{ $colour->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.colour.fields.name') }}
                        </th>
                        <td>
                            {{ $colour->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.colour.fields.code') }}
                        </th>
                        <td>
                            {{ $colour->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.colour.fields.photo') }}
                        </th>
                        <td>
                            @foreach($colour->photo as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.colour.fields.product_brand') }}
                        </th>
                        <td>
                            {{ $colour->product_brand->name ?? '' }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.colours.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection