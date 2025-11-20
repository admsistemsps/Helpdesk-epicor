<?php

namespace App\Http\Controllers;

use App\Models\MasterDivision;
use Illuminate\Http\Request;
use App\Models\MasterDepartment;

class MasterDivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $divisions = MasterDivision::all();
        $departments = MasterDepartment::all();
        return view('master.divisi.index', compact('divisions', 'departments'));
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
            'code' => 'nullable',
            'name' => 'required',
            'department_id' => 'required',
        ]);

        $division = new MasterDivision();
        $division->code = $request->code;
        $division->name = $request->name;
        $division->department_id = $request->department_id;
        $division->save();

        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterDivision $division)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterDivision $division)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterDivision $division)
    {
        $request->validate([
            'code' => 'nullable',
            'name' => 'required',
            'department_id' => 'required',
        ]);

        $division->code = $request->code;
        $division->name = $request->name;
        $division->department_id = $request->department_id;
        $division->save();

        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterDivision $division)
    {
        $division->delete();
        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil dihapus.');
    }
}
