<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Address;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Order;
use App\Models\TaxInvoice;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Order::with(['user', 'customer', 'address', 'channel', 'tax_invoice'])->select(sprintf('%s.*', (new Order)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'order_show';
                $editGate      = 'order_edit';
                $deleteGate    = 'order_delete';
                $crudRoutePart = 'orders';

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
            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->addColumn('customer_first_name', function ($row) {
                return $row->customer ? $row->customer->first_name : '';
            });

            $table->addColumn('address_address_line_1', function ($row) {
                return $row->address ? $row->address->address_line_1 : '';
            });

            $table->addColumn('channel_name', function ($row) {
                return $row->channel ? $row->channel->name : '';
            });

            $table->editColumn('reference', function ($row) {
                return $row->reference ? $row->reference : "";
            });
            $table->editColumn('delivery_address', function ($row) {
                return $row->delivery_address ? $row->delivery_address : "";
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status->description : '';
            });
            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : "";
            });
            $table->editColumn('mutations', function ($row) {
                return $row->mutations ? $row->mutations : "";
            });
            $table->editColumn('tax_invoice_sent', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->tax_invoice_sent ? 'checked' : null) . '>';
            });
            $table->addColumn('tax_invoice_company_name', function ($row) {
                return $row->tax_invoice ? $row->tax_invoice->company_name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'user', 'customer', 'address', 'channel', 'tax_invoice_sent', 'tax_invoice']);

            return $table->make(true);
        }

        $users        = User::get();
        $customers    = Customer::get();
        $addresses    = Address::get();
        $channels     = Channel::get();
        $tax_invoices = TaxInvoice::get();

        return view('admin.orders.index', compact('users', 'customers', 'addresses', 'channels', 'tax_invoices'));
    }

    public function edit(Order $order)
    {
        abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $addresses = Address::all()->pluck('address_line_1', 'id')->prepend(trans('global.pleaseSelect'), '');

        $channels = Channel::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $tax_invoices = TaxInvoice::all()->pluck('company_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $order->load('user', 'customer', 'address', 'channel', 'tax_invoice');

        return view('admin.orders.edit', compact('addresses', 'channels', 'tax_invoices', 'order'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->validated());

        return redirect()->route('admin.orders.index');
    }

    public function show(Order $order)
    {
        abort_if(Gate::denies('order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order->load('user', 'customer', 'address', 'channel', 'tax_invoice', 'orderOrderTrackings', 'orderOrderDetails', 'orderShipments', 'orderPayments', 'ordersTargets');

        return view('admin.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        abort_if(Gate::denies('order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order->delete();

        return back();
    }

    public function massDestroy(MassDestroyOrderRequest $request)
    {
        Order::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
