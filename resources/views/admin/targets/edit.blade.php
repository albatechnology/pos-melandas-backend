@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.target.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.targets.update", [$target->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="start_date">{{ trans('cruds.target.fields.start_date') }}</label>
                <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text" name="start_date" id="start_date" value="{{ old('start_date', $target->start_date) }}">
                @if($errors->has('start_date'))
                    <span class="text-danger">{{ $errors->first('start_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.target.fields.start_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="end_date">{{ trans('cruds.target.fields.end_date') }}</label>
                <input class="form-control date {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="text" name="end_date" id="end_date" value="{{ old('end_date', $target->end_date) }}">
                @if($errors->has('end_date'))
                    <span class="text-danger">{{ $errors->first('end_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.target.fields.end_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.target.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $target->name) }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.target.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="value">{{ trans('cruds.target.fields.value') }}</label>
                <input class="form-control {{ $errors->has('value') ? 'is-invalid' : '' }}" type="number" name="value" id="value" value="{{ old('value', $target->value) }}" step="0.01" required>
                @if($errors->has('value'))
                    <span class="text-danger">{{ $errors->first('value') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.target.fields.value_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.target.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Target::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $target->type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.target.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.target.fields.subject') }}</label>
                <select class="form-control {{ $errors->has('subject') ? 'is-invalid' : '' }}" name="subject" id="subject" required>
                    <option value disabled {{ old('subject', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Target::SUBJECT_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('subject', $target->subject) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('subject'))
                    <span class="text-danger">{{ $errors->first('subject') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.target.fields.subject_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.target.fields.subject_type') }}</label>
                <select class="form-control {{ $errors->has('subject_type') ? 'is-invalid' : '' }}" name="subject_type" id="subject_type" required>
                    <option value disabled {{ old('subject_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Target::SUBJECT_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('subject_type', $target->subject_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('subject_type'))
                    <span class="text-danger">{{ $errors->first('subject_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.target.fields.subject_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.target.fields.value_type') }}</label>
                <select class="form-control {{ $errors->has('value_type') ? 'is-invalid' : '' }}" name="value_type" id="value_type" required>
                    <option value disabled {{ old('value_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Target::VALUE_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('value_type', $target->value_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('value_type'))
                    <span class="text-danger">{{ $errors->first('value_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.target.fields.value_type_helper') }}</span>
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