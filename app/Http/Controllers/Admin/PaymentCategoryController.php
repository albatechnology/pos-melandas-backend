<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPaymentCategoryRequest;
use App\Http\Requests\StorePaymentCategoryRequest;
use App\Http\Requests\UpdatePaymentCategoryRequest;
use App\Models\Company;
use App\Models\PaymentCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentCategoryController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('payment_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentCategories = PaymentCategory::with(['company'])->get();

        return view('admin.paymentCategories.index', compact('paymentCategories'));
    }

    public function create()
    {
        abort_if(Gate::denies('payment_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.paymentCategories.create', compact('companies'));
    }

    public function store(StorePaymentCategoryRequest $request)
    {
        $paymentCategory = PaymentCategory::create($request->validated());

        return redirect()->route('admin.payment-categories.index');
    }

    public function edit(PaymentCategory $paymentCategory)
    {
        abort_if(Gate::denies('payment_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $paymentCategory->load('company');

        return view('admin.paymentCategories.edit', compact('companies', 'paymentCategory'));
    }

    public function update(UpdatePaymentCategoryRequest $request, PaymentCategory $paymentCategory)
    {
        $paymentCategory->update($request->validated());

        return redirect()->route('admin.payment-categories.index');
    }

    public function show(PaymentCategory $paymentCategory)
    {
        abort_if(Gate::denies('payment_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentCategory->load('company', 'paymentCategoryPaymentTypes');

        return view('admin.paymentCategories.show', compact('paymentCategory'));
    }

    public function destroy(PaymentCategory $paymentCategory)
    {
        abort_if(Gate::denies('payment_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyPaymentCategoryRequest $request)
    {
        PaymentCategory::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
