<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySupervisorTypeRequest;
use App\Http\Requests\StoreSupervisorTypeRequest;
use App\Http\Requests\UpdateSupervisorTypeRequest;
use App\Models\SupervisorType;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SupervisorTypeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('supervisor_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $supervisorTypes = SupervisorType::all();

        return view('admin.supervisorTypes.index', compact('supervisorTypes'));
    }

    public function create()
    {
        abort_if(Gate::denies('supervisor_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.supervisorTypes.create');
    }

    public function store(StoreSupervisorTypeRequest $request)
    {
        $supervisorType = SupervisorType::create($request->validated());

        return redirect()->route('admin.supervisor-types.index');
    }

    public function edit(SupervisorType $supervisorType)
    {
        abort_if(Gate::denies('supervisor_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.supervisorTypes.edit', compact('supervisorType'));
    }

    public function update(UpdateSupervisorTypeRequest $request, SupervisorType $supervisorType)
    {
        $supervisorType->update($request->validated());

        return redirect()->route('admin.supervisor-types.index');
    }

    public function show(SupervisorType $supervisorType)
    {
        abort_if(Gate::denies('supervisor_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $supervisorType->load('supervisorTypeUsers');

        return view('admin.supervisorTypes.show', compact('supervisorType'));
    }

    public function destroy(SupervisorType $supervisorType)
    {
        abort_if(Gate::denies('supervisor_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $supervisorType->delete();

        return back();
    }

    public function massDestroy(MassDestroySupervisorTypeRequest $request)
    {
        SupervisorType::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
