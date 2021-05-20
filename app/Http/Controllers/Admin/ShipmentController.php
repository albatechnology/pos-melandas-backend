<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyShipmentRequest;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('shipment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Shipment::with(['order', 'fulfilled_by'])->select(sprintf('%s.*', (new Shipment)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'shipment_show';
                $editGate      = 'shipment_edit';
                $deleteGate    = 'shipment_delete';
                $crudRoutePart = 'shipments';

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
            $table->addColumn('order_reference', function ($row) {
                return $row->order ? $row->order->reference : '';
            });

            $table->addColumn('fulfilled_by_name', function ($row) {
                return $row->fulfilled_by ? $row->fulfilled_by->name : '';
            });

            $table->editColumn('status', function ($row) {
                return $row->status ? Shipment::STATUS_SELECT[$row->status] : '';
            });
            $table->editColumn('note', function ($row) {
                return $row->note ? $row->note : "";
            });
            $table->editColumn('reference', function ($row) {
                return $row->reference ? $row->reference : "";
            });

            $table->rawColumns(['actions', 'placeholder', 'order', 'fulfilled_by']);

            return $table->make(true);
        }

        $orders = Order::get();
        $users  = User::get();

        return view('admin.shipments.index', compact('orders', 'users'));
    }

    public function create()
    {
        abort_if(Gate::denies('shipment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orders = Order::all()->pluck('reference', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.shipments.create', compact('orders'));
    }

    public function store(StoreShipmentRequest $request)
    {
        $shipment = Shipment::create($request->validated());

        return redirect()->route('admin.shipments.index');
    }

    public function edit(Shipment $shipment)
    {
        abort_if(Gate::denies('shipment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $fulfilled_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $shipment->load('order', 'fulfilled_by');

        return view('admin.shipments.edit', compact('fulfilled_bies', 'shipment'));
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment)
    {
        $shipment->update($request->validated());

        return redirect()->route('admin.shipments.index');
    }

    public function show(Shipment $shipment)
    {
        abort_if(Gate::denies('shipment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shipment->load('order', 'fulfilled_by');

        return view('admin.shipments.show', compact('shipment'));
    }

    public function destroy(Shipment $shipment)
    {
        abort_if(Gate::denies('shipment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shipment->delete();

        return back();
    }

    public function massDestroy(MassDestroyShipmentRequest $request)
    {
        Shipment::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
