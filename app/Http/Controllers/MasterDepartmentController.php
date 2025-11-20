<?php

namespace App\Http\Controllers;

use App\Models\MasterDepartment;
use Illuminate\Http\Request;

class MasterDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = MasterDepartment::all();
        return view('master.departemen.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'nullable',
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $department = new MasterDepartment();
        $department->code = $request->code;
        $department->name = $request->name;
        $department->description = $request->description;
        $department->save();

        return redirect()->route('departments.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterDepartment $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterDepartment $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterDepartment $department)
    {
        $validate = $request->validate([
            'code' => 'nullable',
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $department->code = $request->code;
        $department->name = $request->name;
        $department->description = $request->description;
        $department->save();

        return redirect()->route('departments.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterDepartment $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
