<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Channel;
use App\Models\Company;
use App\Models\Role;
use App\Models\SupervisorType;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = User::tenanted()->with(['roles', 'supervisor_type', 'supervisor', 'companies', 'channels'])->select(sprintf('%s.*', (new User)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'user_show';
                $editGate      = 'user_edit';
                $deleteGate    = 'user_delete';
                $crudRoutePart = 'users';

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
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : "";
            });

            $table->editColumn('roles', function ($row) {
                $labels = [];

                foreach ($row->roles as $role) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $role->title);
                }

                return implode(' ', $labels);
            });
            $table->editColumn('type', function ($row) {
                return $row->type ?? '';
            });
            $table->addColumn('supervisor_type_name', function ($row) {
                return $row->supervisor_type ? $row->supervisor_type->name : '';
            });

            $table->addColumn('supervisor_name', function ($row) {
                return $row->supervisor ? $row->supervisor->name : '';
            });

            $table->editColumn('companies', function ($row) {
                $labels = [];

                foreach ($row->companies as $company) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $company->name);
                }

                return implode(' ', $labels);
            });
            $table->editColumn('channels', function ($row) {
                $labels = [];

                foreach ($row->channels as $channel) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $channel->name);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'roles', 'supervisor_type', 'supervisor', 'companies', 'channels']);

            return $table->make(true);
        }

        $roles            = Role::get();
        $supervisor_types = SupervisorType::get();
        $users            = User::get();
        $companies        = Company::get();
        $channels         = Channel::get();

        return view('admin.users.index', compact('roles', 'supervisor_types', 'users', 'companies', 'channels'));
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all()->pluck('title', 'id');

        $supervisor_types = SupervisorType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $supervisors = User::tenanted()->whereIsSupervisor()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $companies = Company::tenanted()->get()->pluck('name', 'id');

        $channels = Channel::tenanted()->get()->pluck('name', 'id');

        return view('admin.users.create', compact('roles', 'supervisor_types', 'supervisors', 'companies', 'channels'));
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        $user->roles()->sync($request->input('roles', []));
        $user->companies()->sync($request->input('companies', []));
        $user->channels()->sync($request->input('channels', []));

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all()->pluck('title', 'id');

        $supervisor_types = SupervisorType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $supervisors = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $companies = Company::all()->pluck('name', 'id');

        $channels = Channel::all()->pluck('name', 'id');

        $user->load('roles', 'supervisor_type', 'supervisor', 'companies', 'channels');

        return view('admin.users.edit', compact('roles', 'supervisor_types', 'supervisors', 'companies', 'channels', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());
        $user->roles()->sync($request->input('roles', []));
        $user->companies()->sync($request->input('companies', []));
        $user->channels()->sync($request->input('channels', []));

        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->load('roles', 'supervisor_type', 'supervisor', 'companies', 'channels', 'userActivities', 'userActivityComments', 'userOrders', 'approvedByPayments', 'fulfilledByShipments', 'fulfilledByInvoices', 'supervisorUsers', 'requestedByStockTransfers', 'approvedByStockTransfers', 'userUserAlerts');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
