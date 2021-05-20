@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.user.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.users.update", [$user->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.user.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="email">{{ trans('cruds.user.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                @if($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.email_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="password">{{ trans('cruds.user.fields.password') }}</label>
                <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password">
                @if($errors->has('password'))
                    <span class="text-danger">{{ $errors->first('password') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.password_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="roles">{{ trans('cruds.user.fields.roles') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('roles') ? 'is-invalid' : '' }}" name="roles[]" id="roles" multiple>
                    @foreach($roles as $id => $roles)
                        <option value="{{ $id }}" {{ (in_array($id, old('roles', [])) || $user->roles->contains($id)) ? 'selected' : '' }}>{{ $roles }}</option>
                    @endforeach
                </select>
                @if($errors->has('roles'))
                    <span class="text-danger">{{ $errors->first('roles') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.roles_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="supervisor_type_id">{{ trans('cruds.user.fields.supervisor_type') }}</label>
                <select class="form-control select2 {{ $errors->has('supervisor_type') ? 'is-invalid' : '' }}" name="supervisor_type_id" id="supervisor_type_id">
                    @foreach($supervisor_types as $id => $supervisor_type)
                        <option value="{{ $id }}" {{ (old('supervisor_type_id') ? old('supervisor_type_id') : $user->supervisor_type->id ?? '') == $id ? 'selected' : '' }}>{{ $supervisor_type }}</option>
                    @endforeach
                </select>
                @if($errors->has('supervisor_type'))
                    <span class="text-danger">{{ $errors->first('supervisor_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.supervisor_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="supervisor_id">{{ trans('cruds.user.fields.supervisor') }}</label>
                <select class="form-control select2 {{ $errors->has('supervisor') ? 'is-invalid' : '' }}" name="supervisor_id" id="supervisor_id">
                    @foreach($supervisors as $id => $supervisor)
                        <option value="{{ $id }}" {{ (old('supervisor_id') ? old('supervisor_id') : $user->supervisor->id ?? '') == $id ? 'selected' : '' }}>{{ $supervisor }}</option>
                    @endforeach
                </select>
                @if($errors->has('supervisor'))
                    <span class="text-danger">{{ $errors->first('supervisor') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.supervisor_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="companies">{{ trans('cruds.user.fields.companies') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('companies') ? 'is-invalid' : '' }}" name="companies[]" id="companies" multiple>
                    @foreach($companies as $id => $companies)
                        <option value="{{ $id }}" {{ (in_array($id, old('companies', [])) || $user->companies->contains($id)) ? 'selected' : '' }}>{{ $companies }}</option>
                    @endforeach
                </select>
                @if($errors->has('companies'))
                    <span class="text-danger">{{ $errors->first('companies') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.companies_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="channels">{{ trans('cruds.user.fields.channels') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('channels') ? 'is-invalid' : '' }}" name="channels[]" id="channels" multiple>
                    @foreach($channels as $id => $channels)
                        <option value="{{ $id }}" {{ (in_array($id, old('channels', [])) || $user->channels->contains($id)) ? 'selected' : '' }}>{{ $channels }}</option>
                    @endforeach
                </select>
                @if($errors->has('channels'))
                    <span class="text-danger">{{ $errors->first('channels') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.channels_helper') }}</span>
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