<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::get();
        return view('master.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $role = Role::get();

        return view('master.users.create', compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        $user = new User();
        $user->uuid = Str::uuid();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        $user->syncRoles((int)$request->role);
        return redirect()->route('master.users.index')->with('success', 'User created successfully!');
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
        $user = User::where('uuid', $id)->first();
        $role = Role::get();
        if (!$user) {
            return redirect()->route('master.users.index')->with('error', 'User not found!');
        }
        return view('master.users.edit', compact('user', 'role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //

        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $id . ',uuid']

        ]);
        $user = User::where('uuid', $id)->first();
        if (!$user) {
            return redirect()->route('master.users.index')->with('error', 'User not found!');
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->syncRoles((int)$request->role);
        if ($request->has('password') && $request->password != null) {
            # code...
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return redirect()->route('master.users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $user = User::where('uuid', $id)->first();
        if (!$user) {
            return response()->json(['success' => true, 'message' => 'User not found!', 'status' => 404]);
        }
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted successfully', 'status' => 200]);
    }
}
