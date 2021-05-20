@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.promo.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.promos.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.promo.fields.id') }}
                        </th>
                        <td>
                            {{ $promo->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promo.fields.name') }}
                        </th>
                        <td>
                            {{ $promo->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promo.fields.description') }}
                        </th>
                        <td>
                            {!! $promo->description !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promo.fields.image') }}
                        </th>
                        <td>
                            @foreach($promo->image as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promo.fields.start_date') }}
                        </th>
                        <td>
                            {{ $promo->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promo.fields.end_date') }}
                        </th>
                        <td>
                            {{ $promo->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promo.fields.promotable_type') }}
                        </th>
                        <td>
                            {{ $promo->promotable_type }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promo.fields.promotable_identifier') }}
                        </th>
                        <td>
                            {{ $promo->promotable_identifier }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promo.fields.company') }}
                        </th>
                        <td>
                            {{ $promo->company->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.promos.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection