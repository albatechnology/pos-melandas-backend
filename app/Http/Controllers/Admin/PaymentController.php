<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPaymentRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Payment::with(['payment_type', 'added_by', 'approved_by', 'order'])->select(sprintf('%s.*', (new Payment)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'payment_show';
                $editGate      = 'payment_edit';
                $deleteGate    = 'payment_delete';
                $crudRoutePart = 'payments';

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
            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : "";
            });
            $table->addColumn('payment_type_name', function ($row) {
                return $row->payment_type ? $row->payment_type->name : '';
            });

            $table->editColumn('reference', function ($row) {
                return $row->reference ? $row->reference : "";
            });
            $table->addColumn('added_by_name', function ($row) {
                return $row->added_by ? $row->added_by->name : '';
            });

            $table->addColumn('approved_by_name', function ($row) {
                return $row->approved_by ? $row->approved_by->name : '';
            });

            $table->editColumn('proof', function ($row) {
                if (!$row->proof) {
                    return '';
                }

                $links = [];

                foreach ($row->proof as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>';
                }

                return implode(', ', $links);
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? Payment::STATUS_SELECT[$row->status] : '';
            });
            $table->editColumn('reason', function ($row) {
                return $row->reason ? $row->reason : "";
            });
            $table->addColumn('order_reference', function ($row) {
                return $row->order ? $row->order->reference : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'payment_type', 'added_by', 'approved_by', 'proof', 'order']);

            return $table->make(true);
        }

        $payment_types = PaymentType::get();
        $users         = User::get();
        $orders        = Order::get();

        return view('admin.payments.index', compact('payment_types', 'users', 'orders'));
    }

    public function create()
    {
        abort_if(Gate::denies('payment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment_types = PaymentType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $added_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $approved_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $orders = Order::all()->pluck('reference', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.payments.create', compact('payment_types', 'added_bies', 'approved_bies', 'orders'));
    }

    public function store(StorePaymentRequest $request)
    {
        $payment = Payment::create($request->validated());

        foreach ($request->input('proof', []) as $file) {
            $payment->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('proof');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $payment->id]);
        }

        return redirect()->route('admin.payments.index');
    }

    public function edit(Payment $payment)
    {
        abort_if(Gate::denies('payment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment_types = PaymentType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $added_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $approved_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $orders = Order::all()->pluck('reference', 'id')->prepend(trans('global.pleaseSelect'), '');

        $payment->load('payment_type', 'added_by', 'approved_by', 'order');

        return view('admin.payments.edit', compact('payment_types', 'added_bies', 'approved_bies', 'orders', 'payment'));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment->update($request->validated());

        if (count($payment->proof) > 0) {
            foreach ($payment->proof as $media) {
                if (!in_array($media->file_name, $request->input('proof', []))) {
                    $media->delete();
                }
            }
        }

        $media = $payment->proof->pluck('file_name')->toArray();

        foreach ($request->input('proof', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $payment->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('proof');
            }
        }

        return redirect()->route('admin.payments.index');
    }

    public function show(Payment $payment)
    {
        abort_if(Gate::denies('payment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment->load('payment_type', 'added_by', 'approved_by', 'order');

        return view('admin.payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        abort_if(Gate::denies('payment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment->delete();

        return back();
    }

    public function massDestroy(MassDestroyPaymentRequest $request)
    {
        Payment::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('payment_create') && Gate::denies('payment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Payment();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
