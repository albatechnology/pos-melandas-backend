<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProductUnitRequest;
use App\Http\Requests\StoreProductUnitRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Models\Product;
use App\Models\ProductUnit;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductUnitsController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('product_unit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductUnit::with(['product'])->select(sprintf('%s.*', (new ProductUnit)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'product_unit_show';
                $editGate      = 'product_unit_edit';
                $deleteGate    = 'product_unit_delete';
                $crudRoutePart = 'product-units';

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
            $table->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : '';
            });

            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });
            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : "";
            });
            $table->editColumn('is_active', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->is_active ? 'checked' : null) . '>';
            });

            $table->rawColumns(['actions', 'placeholder', 'product', 'is_active']);

            return $table->make(true);
        }

        $products = Product::get();

        return view('admin.productUnits.index', compact('products'));
    }

    public function create()
    {
        abort_if(Gate::denies('product_unit_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.productUnits.create', compact('products'));
    }

    public function store(StoreProductUnitRequest $request)
    {
        $productUnit = ProductUnit::create($request->validated());

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $productUnit->id]);
        }

        return redirect()->route('admin.product-units.index');
    }

    public function edit(ProductUnit $productUnit)
    {
        abort_if(Gate::denies('product_unit_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $productUnit->load('product');

        return view('admin.productUnits.edit', compact('products', 'productUnit'));
    }

    public function update(UpdateProductUnitRequest $request, ProductUnit $productUnit)
    {
        $productUnit->update($request->validated());

        return redirect()->route('admin.product-units.index');
    }

    public function show(ProductUnit $productUnit)
    {
        abort_if(Gate::denies('product_unit_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productUnit->load('product', 'productUnitItemProductUnits', 'productUnitOrderDetails', 'productunitsPromos');

        return view('admin.productUnits.show', compact('productUnit'));
    }

    public function destroy(ProductUnit $productUnit)
    {
        abort_if(Gate::denies('product_unit_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productUnit->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductUnitRequest $request)
    {
        ProductUnit::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('product_unit_create') && Gate::denies('product_unit_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new ProductUnit();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
