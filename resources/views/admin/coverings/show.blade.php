@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.covering.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.coverings.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.covering.fields.id') }}
                        </th>
                        <td>
                            {{ $covering->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.covering.fields.name') }}
                        </th>
                        <td>
                            {{ $covering->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.covering.fields.code') }}
                        </th>
                        <td>
                            {{ $covering->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.covering.fields.photo') }}
                        </th>
                        <td>
                            @foreach($covering->photo as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.coverings.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection