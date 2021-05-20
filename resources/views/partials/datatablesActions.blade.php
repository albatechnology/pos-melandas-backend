@can($viewGate ?? false)
    <a class="btn btn-xs btn-primary" href="{{ $viewRoute ?? route('admin.' . $crudRoutePart . '.show', $row->id) }}">
        {{ trans('global.view') }}
    </a>
@endcan
@can($editGate ?? false)
    <a class="btn btn-xs btn-info" href="{{ $editRoute ?? route('admin.' . $crudRoutePart . '.edit', $row->id) }}">
        {{ trans('global.edit') }}
    </a>
@endcan
@can($deleteGate ?? false)
    <form action="{{ $deleteRoute ?? route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST"
          onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
    </form>
@endcan