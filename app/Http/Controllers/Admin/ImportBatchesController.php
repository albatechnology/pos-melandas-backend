<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Import\ImportBatchStatus;
use App\Enums\Import\ImportBatchType;
use App\Enums\Import\ImportLineStatus;
use App\Enums\Import\ImportMode;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCompanyRequest;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\ImportBatch;
use App\Models\ImportLine;
use App\Services\FileImportService;
use Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ImportBatchesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('import_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ImportBatch::tenanted()->select(sprintf('%s.*', (new ImportBatch)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'import_show';
                $crudRoutePart = 'import-batches';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ?? "";
            });
            $table->editColumn('type', function ($row) {
                return $row->type?->description ?? "";
            });
            $table->editColumn('filename', function ($row) {
                return $row->filename ?? "";
            });
            $table->editColumn('status', function ($row) {
                return $row->status?->description ?? "";
            });
            $table->editColumn('summary', function ($row) {
                return $row->summary ?? "";
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.importBatches.index');
    }

    public function create()
    {
        abort_if(Gate::denies('import_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::tenanted()->get()->pluck('name', 'id');
        $types     = ImportBatchType::getInstances();

        return view('admin.importBatches.create', compact('companies', 'types'));
    }

    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->validated());

        return redirect()->route('admin.companies.index');
    }

    public function edit(Company $company)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company->update($request->validated());

        return redirect()->route('admin.companies.index');
    }

    public function show($batch)
    {
        abort_if(Gate::denies('import_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $batch = ImportBatch::tenanted()->with('importLines')->findOrFail($batch);
        return view('admin.importBatches.show', compact('batch'));
    }

    public function destroy(Company $company)
    {
        abort_if(Gate::denies('company_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $company->delete();

        return back();
    }

    public function massDestroy(MassDestroyCompanyRequest $request)
    {
        Company::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param $batch_id
     * @return RedirectResponse
     * @throws Throwable
     */
    public function processUpdate($batch_id)
    {
        $batch = ImportBatch::tenanted()->findOrFail($batch_id);

        if ($batch->status->isNot(ImportBatchStatus::PREVIEW)) {
            return redirect()->back();
        }

        $batch->update(['status' => ImportBatchStatus::IMPORTING]);
        FileImportService::processImportBatch($batch, ImportMode::UPDATE_DUPLICATE());

        return redirect()->back();
    }

    /**
     * @param $batch_id
     * @return RedirectResponse
     * @throws Throwable
     */
    public function processSkip($batch_id)
    {
        $batch = ImportBatch::tenanted()->findOrFail($batch_id);

        if ($batch->status->isNot(ImportBatchStatus::PREVIEW)) {
            return redirect()->back();
        }

        $batch->update(['status' => ImportBatchStatus::IMPORTING]);
        FileImportService::processImportBatch($batch, ImportMode::SKIP_DUPLICATE());
        return redirect()->back();
    }

    /**
     * @param ImportBatch $import_batch
     * @return RedirectResponse
     */
    public function processCancel(ImportBatch $import_batch)
    {
        $import_batch->cancel();
        return redirect()->back();
    }

    public function ajaxStatus($id)
    {
        $batch = ImportBatch::tenanted()->findOrFail($id);
        return ['status' => $batch->status->value];
    }

    public function ajaxLineCount($id, $batch_status)
    {
        if (ImportBatchStatus::fromValue((int)$batch_status)->is(ImportBatchStatus::GENERATING_PREVIEW)) {
            $count = ImportLine::where('import_batch_id', $id)
                ->count();
        }

        if (ImportBatchStatus::fromValue((int)$batch_status)->is(ImportBatchStatus::IMPORTING)) {
            $count = ImportLine::where('import_batch_id', $id)
                ->whereNotIn('status', [ImportLineStatus::PREVIEW])
                ->count();
        }

        return ['count' => $count ?? 0];
    }


}
