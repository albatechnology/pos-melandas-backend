<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductUnit;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class OrderDetailController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('order_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = OrderDetail::with(['product_unit', 'order'])->select(sprintf('%s.*', (new OrderDetail)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'order_detail_show';
                $editGate      = 'order_detail_edit';
                $deleteGate    = 'order_detail_delete';
                $crudRoutePart = 'order-details';

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
            $table->addColumn('product_unit_name', function ($row) {
                return $row->product_unit ? $row->product_unit->name : '';
            });

            $table->addColumn('order_reference', function ($row) {
                return $row->order ? $row->order->reference : '';
            });

            $table->editColumn('product_detail', function ($row) {
                return $row->product_detail ? $row->product_detail : "";
            });
            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : "";
            });
            $table->editColumn('mutations', function ($row) {
                return $row->mutations ? $row->mutations : "";
            });
            $table->editColumn('unit_price', function ($row) {
                return $row->unit_price ? $row->unit_price : "";
            });
            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : "";
            });

            $table->rawColumns(['actions', 'placeholder', 'product_unit', 'order']);

            return $table->make(true);
        }

        $product_units = ProductUnit::get();
        $orders        = Order::get();

        return view('admin.orderDetails.index', compact('product_units', 'orders'));
    }

    public function show(OrderDetail $orderDetail)
    {
        abort_if(Gate::denies('order_detail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orderDetail->load('product_unit', 'order', 'orderDetailsTargets');

        return view('admin.orderDetails.show', compact('orderDetail'));
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('order_detail_create') && Gate::denies('order_detail_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new OrderDetail();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
