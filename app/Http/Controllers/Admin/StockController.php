<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\UpdateStockRequest;
use App\Models\Channel;
use App\Models\Stock;
use Exception;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('stock_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Stock::query()
                ->tenanted()
                ->with(['channel', 'productUnit'])
                ->select(sprintf('%s.*', (new Stock)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'stock_show';
                $editGate      = 'stock_edit';
                $deleteGate    = 'stock_delete';
                $crudRoutePart = 'stocks';

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
            $table->addColumn('channel_name', function ($row) {
                return $row->channel ? $row->channel->name : '';
            });
            $table->addColumn('product_unit_name', function ($row) {
                return $row->productUnit ? $row->productUnit->name : '';
            });

            $table->editColumn('stock', function ($row) {
                return $row->stock;
            });

            $table->rawColumns(['actions', 'placeholder', 'channel']);

            return $table->make(true);
        }

        $channels = Channel::tenanted()->get();

        return view('admin.stocks.index', compact('channels'));
    }

    public function edit(Stock $stock)
    {
        abort_if(Gate::denies('stock_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stock->load('channel', 'productUnit');

        return view('admin.stocks.edit', compact('stock'));
    }

    public function update(UpdateStockRequest $request, Stock $stock)
    {
        $increment = $request->get('increment');

        try {
            $stock->addStock($increment);
        } catch (Exception) {
            $errors = new MessageBag(
                [
                    'increment' => ['Insufficient stock!']
                ]
            );
            return redirect()->back()->withErrors($errors);
        }

        return redirect()->route('admin.stocks.index');
    }

    public function show(Stock $stock)
    {
        abort_if(Gate::denies('stock_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stock->load('channel', 'productUnit', 'stockFromStockTransfers', 'stockToStockTransfers');

        return view('admin.stocks.show', compact('stock'));
    }
}
