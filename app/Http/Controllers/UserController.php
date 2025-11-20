<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MasterPosition;
use App\Models\MasterDivision;
use App\Models\MasterDepartment;
use App\Models\MasterRole;
use App\Models\MasterSite;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with(['position.division.department', 'role', 'division', 'department'])->get();
        $positions = MasterPosition::all();
        $divisions = MasterDivision::all();
        $departments = MasterDepartment::with('divisions')->get();
        $roles = MasterRole::all();
        $sites = MasterSite::all();

        return view('master.users.index', compact('users', 'positions', 'divisions', 'departments', 'roles', 'sites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $positions = MasterPosition::all();
        $roles = MasterRole::all();
        $sites = MasterSite::all();

        return view('master.users.create', compact('users', 'positions', 'roles', 'sites'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'email' => $request->filled('email')
                ? $request->email
                : $request->username . '@gmail.com',
        ]);

        $request->validate([
            'username' => 'required|unique:users,username',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'is_active' => 'nullable|in:1,0',
            'position_id' => 'required|exists:master_positions,id',
            'role_id' => 'nullable|exists:master_roles,id',
            'master_site_id' => 'nullable|exists:master_sites,id',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $status = $request->boolean('is_active') ? 0 : 1;
        $position = MasterPosition::with('division.department')->findOrFail($request->position_id);

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'status' => $status,
            'role_id' => $request->role_id,
            'position_id' => $position->id,
            'division_id' => $position->division_id,
            'department_id' => $position->division->department_id ?? null,
            'master_site_id' => $request->master_site_id,
            'profile_photo_path' => $avatarPath,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $positions = MasterPosition::all();
        $roles = MasterRole::all();
        $sites = MasterSite::all();

        return view('master.users.edit', compact('user', 'positions', 'roles', 'sites'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->merge([
            'email' => $request->filled('email')
                ? $request->email
                : $request->username . '@gmail.com',
        ]);

        $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'is_active' => 'nullable|in:1,0',
            'role_id' => 'nullable|exists:master_roles,id',
            'position_id' => 'required|exists:master_positions,id',
            'master_site_id' => 'nullable|exists:master_sites,id',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
        ]);

        $avatarPath = $user->profile_photo_path;
        if ($request->hasFile('avatar')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $status = $request->boolean('is_active') ? 0 : 1;
        $position = MasterPosition::with('division.department')->findOrFail($request->position_id);

        $user->update([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'status' => $status,
            'role_id' => $request->role_id,
            'position_id' => $position->id,
            'division_id' => $position->division_id,
            'department_id' => $position->department_id ?? null,
            'master_site_id' => $request->master_site_id,
            'profile_photo_path' => $avatarPath,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
