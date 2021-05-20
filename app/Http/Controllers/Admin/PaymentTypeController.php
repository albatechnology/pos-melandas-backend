<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPaymentTypeRequest;
use App\Http\Requests\StorePaymentTypeRequest;
use App\Http\Requests\UpdatePaymentTypeRequest;
use App\Models\Company;
use App\Models\PaymentCategory;
use App\Models\PaymentType;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentTypeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('payment_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentTypes = PaymentType::with(['payment_category', 'company'])->get();

        $payment_categories = PaymentCategory::get();

        $companies = Company::get();

        return view('admin.paymentTypes.index', compact('paymentTypes', 'payment_categories', 'companies'));
    }

    public function create()
    {
        abort_if(Gate::denies('payment_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment_categories = PaymentCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.paymentTypes.create', compact('payment_categories', 'companies'));
    }

    public function store(StorePaymentTypeRequest $request)
    {
        $paymentType = PaymentType::create($request->validated());

        return redirect()->route('admin.payment-types.index');
    }

    public function edit(PaymentType $paymentType)
    {
        abort_if(Gate::denies('payment_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment_categories = PaymentCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $paymentType->load('payment_category', 'company');

        return view('admin.paymentTypes.edit', compact('payment_categories', 'companies', 'paymentType'));
    }

    public function update(UpdatePaymentTypeRequest $request, PaymentType $paymentType)
    {
        $paymentType->update($request->validated());

        return redirect()->route('admin.payment-types.index');
    }

    public function show(PaymentType $paymentType)
    {
        abort_if(Gate::denies('payment_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentType->load('payment_category', 'company', 'paymentTypePayments');

        return view('admin.paymentTypes.show', compact('paymentType'));
    }

    public function destroy(PaymentType $paymentType)
    {
        abort_if(Gate::denies('payment_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentType->delete();

        return back();
    }

    public function massDestroy(MassDestroyPaymentTypeRequest $request)
    {
        PaymentType::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
