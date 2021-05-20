<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyLeadRequest;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Lead;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class LeadsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Lead::tenanted()->with(['customer', 'channel'])->select(sprintf('%s.*', (new Lead)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'lead_show';
                $editGate      = 'lead_edit';
                $deleteGate    = 'lead_delete';
                $crudRoutePart = 'leads';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('type', function ($row) {
                return $row->type->description ?? '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status->description ?? '';
            });
            $table->editColumn('is_new_customer', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->is_new_customer ? 'checked' : null) . '>';
            });
            $table->editColumn('label', function ($row) {
                return $row->label ? $row->label : "";
            });
            $table->addColumn('customer_first_name', function ($row) {
                return $row->customer ? $row->customer->first_name : '';
            });

            $table->addColumn('channel_name', function ($row) {
                return $row->channel ? $row->channel->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'is_new_customer', 'customer', 'channel']);

            return $table->make(true);
        }

        $customers = Customer::get();
        $channels  = Channel::get();

        return view('admin.leads.index', compact('customers', 'channels'));
    }

    public function create()
    {
        abort_if(Gate::denies('lead_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customers = Customer::all()->pluck('first_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $channels = Channel::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.leads.create', compact('customers', 'channels'));
    }

    public function store(StoreLeadRequest $request)
    {
        $lead = Lead::create($request->validated());

        return redirect()->route('admin.leads.index');
    }

    public function edit(Lead $lead)
    {
        abort_if(Gate::denies('lead_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customers = Customer::all()->pluck('first_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $channels = Channel::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $lead->load('customer', 'channel');

        return view('admin.leads.edit', compact('customers', 'channels', 'lead'));
    }

    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $lead->update($request->validated());

        return redirect()->route('admin.leads.index');
    }

    public function show(Lead $lead)
    {
        abort_if(Gate::denies('lead_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lead->load('customer', 'channel', 'leadActivities');

        return view('admin.leads.show', compact('lead'));
    }

    public function destroy(Lead $lead)
    {
        abort_if(Gate::denies('lead_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lead->delete();

        return back();
    }

    public function massDestroy(MassDestroyLeadRequest $request)
    {
        Lead::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
