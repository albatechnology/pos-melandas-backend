@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.target.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.targets.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.target.fields.id') }}
                        </th>
                        <td>
                            {{ $target->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.target.fields.start_date') }}
                        </th>
                        <td>
                            {{ $target->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.target.fields.end_date') }}
                        </th>
                        <td>
                            {{ $target->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.target.fields.name') }}
                        </th>
                        <td>
                            {{ $target->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.target.fields.value') }}
                        </th>
                        <td>
                            {{ $target->value }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.target.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\Target::TYPE_SELECT[$target->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.target.fields.subject') }}
                        </th>
                        <td>
                            {{ App\Models\Target::SUBJECT_SELECT[$target->subject] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.target.fields.subject_type') }}
                        </th>
                        <td>
                            {{ App\Models\Target::SUBJECT_TYPE_SELECT[$target->subject_type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.target.fields.value_type') }}
                        </th>
                        <td>
                            {{ App\Models\Target::VALUE_TYPE_SELECT[$target->value_type] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.targets.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection