@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.importBatch.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.importBatch.fields.id') }}
                        </th>
                        <td>
                            {{ $batch->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.importBatch.fields.filename') }}
                        </th>
                        <td>
                            {{ $batch->filename }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.importBatch.fields.status') }}
                        </th>
                        <td>
                            <span class="external-event bg-{{ $batch->status->getColourSchema() }}">
                                @if($batch->status->isLoading())
                                    <i class="fa fa-spin fa-sync-alt"></i>
                                @endif
                                {{ $batch->status->description ?? '' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.importBatch.fields.type') }}
                        </th>
                        <td>
                            {{ $batch->type?->description ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.importBatch.fields.summary') }}
                        </th>
                        <td>
                            {{ $batch->summary ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.importBatch.fields.preview_summary') }}
                        </th>
                        <td>
                            {{ $batch->preview_summary ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.importBatch.fields.errors') }}
                        </th>
                        <td>
                            @if(!empty($batch->errors ?? []))
                                <ul>
                                    @foreach($batch->errors as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.importBatch.fields.updated_at') }}
                        </th>
                        <td>
                            {{ $batch->updated_at }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.importBatch.fields.created_at') }}
                        </th>
                        <td>
                            {{ $batch->created_at }}
                        </td>
                    </tr>
                    </tbody>
                </table>

                @if($batch->status->isImporting())
                    <div class="progress">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                             id="import-progress" role="progressbar"
                             aria-valuenow="0%" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                            0% Complete
                        </div>
                    </div>
                    <br>
                @endif

                <div class="form-group">
                    @if($batch->processable())
                        <a class="btn btn-primary" href="{{ route('admin.import-batches.processUpdate', $batch->id) }}">
                            Process (Update duplicates)
                        </a>
                        <a class="btn btn-secondary" href="{{ route('admin.import-batches.processSkip', $batch->id) }}">
                            Process (Skip duplicates)
                        </a>
                    @endif
                    @if($batch->status->cancellable())
                        <a class="btn btn-danger" href="{{ route('admin.import-batches.processCancel', $batch->id) }}">
                            {{ trans('global.cancel') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.importLine.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ImportBatchLine">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th width="50">
                            {{ trans('cruds.importLine.fields.row') }}
                        </th>
                        <th>
                            {{ trans('cruds.importLine.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.importLine.fields.preview_status') }}
                        </th>
                        <th>
                            {{ trans('cruds.importLine.fields.errors') }}
                        </th>
                        <th>
                            {{ trans('cruds.productBrand.fields.name') }}
                        </th>
                        <th>

                        </th>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <input class="search w-100" type="text" strict="true"
                                   placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach(\App\Enums\Import\ImportLineStatus::getInstances() as $enum)
                                    <option value="{{ $enum->value }}">{{ $enum->description }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach(\App\Enums\Import\ImportLinePreviewStatus::getInstances() as $enum)
                                    <option value="{{ $enum->value }}">{{ $enum->description }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                        </td>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.import-batches.import-lines.index', $batch->id) }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder'},
                    {data: 'row', name: 'row'},
                    {data: 'status', name: 'status'},
                    {data: 'preview_status', name: 'preview_status'},
                    {data: 'errors', name: 'errors'},
                    {data: 'name', name: 'data.name'},
                    {data: 'actions', name: '{{ trans('global.actions') }}'}
                ],
                orderCellsTop: true,
                order: [[1, 'asc']],
                pageLength: 25,
            };
            let table = $('.datatable-ImportBatchLine').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

            let visibleColumnsIndexes = null;
            $('.datatable thead').on('input', '.search', function () {
                let strict = $(this).attr('strict') || false
                let value = strict && this.value ? "^" + this.value + "$" : this.value

                let index = $(this).parent().index()
                if (visibleColumnsIndexes !== null) {
                    index = visibleColumnsIndexes[index]
                }

                table
                    .column(index)
                    .search(value, strict)
                    .draw()
            });
            table.on('column-visibility.dt', function (e, settings, column, state) {
                visibleColumnsIndexes = []
                table.columns(":visible").every(function (colIdx) {
                    visibleColumnsIndexes.push(colIdx);
                });
            })

            let polling = true;

            @if($batch->status->isGeneratingPreview())
            (function poll() {
                setTimeout(function () {
                    $.ajax({
                        url: "{{ route('admin.import-batches.ajax.status', $batch->id) }}",
                        type: "GET",
                        success: function (data) {
                            // if status has changed, refresh page
                            if (data.status != {{ $batch->status->value }}) {
                                window.location.reload()
                            }
                        },
                        dataType: "json",
                        complete: function () {
                            if (polling) {
                                poll()
                            }
                        },
                        timeout: 2000
                    })
                }, 5000);
            })();
            @endif

                    @if($batch->status->isImporting())
            (function poll() {
                setTimeout(function () {
                    $.ajax({
                        url: "{{ route('admin.import-batches.ajax.count', [$batch->id, $batch->status->value]) }}",
                        type: "GET",
                        success: function (data) {

                            let total = {{ \App\Models\ImportLine::where('import_batch_id', $batch->id)->count() }};

                            if (total == data.count) {
                                window.location.reload()
                            } else {
                                let percent = Math.floor(100 / total * data.count);
                                $('#import-progress').attr('aria-valuenow', percent).css('width', percent + '%').html(percent + '% complete');
                            }
                        },
                        dataType: "json",
                        complete: function () {
                            if (polling) {
                                poll()
                            }
                        },
                        timeout: 2000
                    })
                }, 5000);
            })();
            @endif
        })

    </script>
@endsection
