<?php

namespace App\Http\Controllers;

use App\Models\PermissionGroup;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $roles = Role::get();
        return view('master.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $permissionsGroup = PermissionGroup::get();
        return view('master.roles.create', compact('permissionsGroup'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|unique:roles,name',
            'guard_name' => 'required',
            'permissions' => 'required|array',
        ]);
        try {
            $permissions = array_map('intval', $request->permissions);
            DB::beginTransaction();
            $role = new Role();
            $role->uuid = Str::uuid();
            $role->name = $request->name;
            $role->guard_name = $request->guard_name;
            $role->save();
            $role->syncPermissions($permissions);
            DB::commit();
            return redirect()->route('master.roles.index')->with('success', 'Role created successfully');
        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $permissionsGroup = PermissionGroup::get();
        $role = Role::where('uuid', $id)->first();
        if (!$role) {
            # code...
            return redirect()->route('master.roles.index')->with('error', 'Roles not found!');
        }
        $permissions = $role->permissions->pluck('id')->toArray();

        return view('master.roles.edit', compact('permissionsGroup', 'role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id . ',uuid',
            'guard_name' => 'required',
            'permissions' => 'required|array',
        ]);
        try {
            $permissions = array_map('intval', $request->permissions);
            DB::beginTransaction();
            $role = Role::where('uuid', $id)->first();
            if (!$role) {
                # code...
                return redirect()->route('master.roles.index')->with('error', 'Roles not found!');
            }
            $role->name = $request->name;
            $role->guard_name = $request->guard_name;
            $role->save();
            $role->syncPermissions($permissions);
            DB::commit();
            return redirect()->route('master.roles.index')->with('success', 'Role update successfully');
        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $role = Role::where('uuid', $id)->first();
        if (!$role) {
            # code...
            return redirect()->route('master.roles.index')->with('error', 'Roles not found!');
        }
        try {
            DB::beginTransaction();
            $role->syncPermissions([]); // remove all permissions from the role first. Then delete the role.
            $role->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Role deleted', 'status' => 200]);
        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);
        }
    }
}
