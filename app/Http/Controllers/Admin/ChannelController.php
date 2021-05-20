<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyChannelRequest;
use App\Http\Requests\StoreChannelRequest;
use App\Http\Requests\UpdateChannelRequest;
use App\Models\Channel;
use App\Models\ChannelCategory;
use App\Models\Company;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ChannelController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('channel_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Channel::with(['channel_category', 'company'])->select(sprintf('%s.*', (new Channel)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'channel_show';
                $editGate      = 'channel_edit';
                $deleteGate    = 'channel_delete';
                $crudRoutePart = 'channels';

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
            $table->addColumn('channel_category_name', function ($row) {
                return $row->channel_category ? $row->channel_category->name : '';
            });

            $table->addColumn('company_name', function ($row) {
                return $row->company ? $row->company->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'channel_category', 'company']);

            return $table->make(true);
        }

        return view('admin.channels.index');
    }

    public function create()
    {
        abort_if(Gate::denies('channel_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $channel_categories = ChannelCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.channels.create', compact('channel_categories', 'companies'));
    }

    public function store(StoreChannelRequest $request)
    {
        $channel = Channel::create($request->validated());

        return redirect()->route('admin.channels.index');
    }

    public function edit(Channel $channel)
    {
        abort_if(Gate::denies('channel_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $channel_categories = ChannelCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $channel->load('channel_category', 'company');

        return view('admin.channels.edit', compact('channel_categories', 'companies', 'channel'));
    }

    public function update(UpdateChannelRequest $request, Channel $channel)
    {
        $channel->update($request->validated());

        return redirect()->route('admin.channels.index');
    }

    public function show(Channel $channel)
    {
        abort_if(Gate::denies('channel_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $channel->load('channel_category', 'company', 'channelCatalogues', 'channelOrders', 'channelStocks', 'channelLeads', 'channelsProducts', 'channelsUsers');

        return view('admin.channels.show', compact('channel'));
    }

    public function destroy(Channel $channel)
    {
        abort_if(Gate::denies('channel_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $channel->delete();

        return back();
    }

    public function massDestroy(MassDestroyChannelRequest $request)
    {
        Channel::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
