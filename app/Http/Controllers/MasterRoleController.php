<?php

namespace App\Http\Controllers;

use App\Models\MasterRole;
use Illuminate\Http\Request;
use App\Models\MasterDepartment;

class MasterRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = MasterRole::all();

        return view('master.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:master_roles,name',
            'description' => 'nullable',
            'level' => 'nullable',
        ]);

        $role = MasterRole::create([
            'description' => $request->description,
            'name' => $request->name,
            'level' => $request->level
        ]);

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(masterRole $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterRole $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterRole $role)
    {
        $request->validate([
            'name' => 'required|unique:master_roles,name,' . $role->id,
            'description' => 'nullable',
            'level' => 'nullable',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'level' => $request->level
        ]);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterRole $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
