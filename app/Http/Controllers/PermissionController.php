<?php

namespace App\Http\Controllers;

use App\Models\PermissionGroup;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $permissions = Permission::get();
        $permissionGroup = PermissionGroup::get();
        return view('master.permissions.index', compact('permissions', 'permissionGroup'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('master.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        if ($request->ajax()) {
            $request->validate([
                'name' => 'required|unique:permissions,name',
                'guard_name' => 'required',
            ]);
        } else {

            $request->validate([
                'name' => 'required|unique:permission_groups,name',
            ]);
        }
        try {
            DB::beginTransaction();
            if ($request->ajax()) {
                # code...
                $permission = new Permission();
                $permission->uuid = Str::uuid();
                $permission->name = $request->name;
                $permission->guard_name = 'web';
                $permission->permission_group_id = $request->permission_group_id;
                $permission->save();
                DB::commit();

                return response()->json(['success' => 'Movie Schedule created successfully', 'status' => 200]);
            } else {

                $permissionGroup = new PermissionGroup();
                $permissionGroup->uuid = Str::uuid();
                $permissionGroup->name = $request->name;
                $permissionGroup->description = $request->description;

                $permissionGroup->save();

                $permission = new Permission();
                $permission->uuid = Str::uuid();
                $permission->name = $request->name;
                $permission->guard_name = 'web';
                $permission->permission_group_id = $permissionGroup->uuid;
                $permission->save();
            }
            DB::commit();
            return redirect()->route('master.permissions.index')->with('success', 'Permission created successfully');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('master.permissions.create')->with('error', 'Permission name already exists!');
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
        $permissionGroup = PermissionGroup::where('uuid', $id)->first();
        if (!$permissionGroup) {
            return redirect()->route('master.permissions.index')->with('error', 'Permission not found!');
        }
        return view('master.permissions.edit', compact('permissionGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        if ($request->ajax()) {
            # code...
        } else {

            $request->validate([
                'name' => 'required|unique:users,name,' . $id . ',uuid',
            ]);
            $permissionGroup = PermissionGroup::where('uuid', $id)->first();
            if (!$permissionGroup) {
                return redirect()->route('master.permissions.index')->with('error', 'Permission not found!');
            }
            $permissionGroup->name = $request->name;
            $permissionGroup->description = $request->description;

            $permissionGroup->save();
            return redirect()->route('master.permissions.edit', $permissionGroup->uuid)->with('success', 'Permission updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $permission = Permission::where('uuid', $id)->first();
        if (!$permission) {
            return response()->json(['success' => true, 'message' => 'Permission not found!', 'status' => 404]);
        }
        $permission->delete();
        return response()->json(['success' => true, 'message' => 'Permission deleted successfully', 'status' => 200]);
    }
}
