<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyTargetRequest;
use App\Http\Requests\StoreTargetRequest;
use App\Http\Requests\UpdateTargetRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Target;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class TargetController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('target_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Target::with(['orders', 'order_details'])->select(sprintf('%s.*', (new Target)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'target_show';
                $editGate      = 'target_edit';
                $deleteGate    = 'target_delete';
                $crudRoutePart = 'targets';

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

            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });
            $table->editColumn('value', function ($row) {
                return $row->value ? $row->value : "";
            });
            $table->editColumn('type', function ($row) {
                return $row->type ? Target::TYPE_SELECT[$row->type] : '';
            });
            $table->editColumn('subject', function ($row) {
                return $row->subject ? Target::SUBJECT_SELECT[$row->subject] : '';
            });
            $table->editColumn('subject_type', function ($row) {
                return $row->subject_type ? Target::SUBJECT_TYPE_SELECT[$row->subject_type] : '';
            });
            $table->editColumn('value_type', function ($row) {
                return $row->value_type ? Target::VALUE_TYPE_SELECT[$row->value_type] : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        $orders        = Order::get();
        $order_details = OrderDetail::get();

        return view('admin.targets.index', compact('orders', 'order_details'));
    }

    public function create()
    {
        abort_if(Gate::denies('target_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.targets.create');
    }

    public function store(StoreTargetRequest $request)
    {
        $target = Target::create($request->validated());

        return redirect()->route('admin.targets.index');
    }

    public function edit(Target $target)
    {
        abort_if(Gate::denies('target_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $target->load('orders', 'order_details');

        return view('admin.targets.edit', compact('target'));
    }

    public function update(UpdateTargetRequest $request, Target $target)
    {
        $target->update($request->validated());

        return redirect()->route('admin.targets.index');
    }

    public function show(Target $target)
    {
        abort_if(Gate::denies('target_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $target->load('orders', 'order_details');

        return view('admin.targets.show', compact('target'));
    }

    public function destroy(Target $target)
    {
        abort_if(Gate::denies('target_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $target->delete();

        return back();
    }

    public function massDestroy(MassDestroyTargetRequest $request)
    {
        Target::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
