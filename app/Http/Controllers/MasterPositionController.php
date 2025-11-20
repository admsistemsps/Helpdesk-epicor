<?php

namespace App\Http\Controllers;

use App\Models\MasterPosition;
use Illuminate\Http\Request;
use App\Models\masterDivision;
use App\Models\MasterRole;
use App\Models\MasterDepartment;

class MasterPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = MasterPosition::all();
        $divisions = MasterDivision::all();
        $departments = MasterDepartment::with('divisions')->get();
        return view('master.posisi.index', compact('positions', 'divisions', 'departments'));
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
            'name' => 'required',
            'description' => 'nullable',
            'department_id' => 'nullable|exists:master_departments,id',
            'division_id' => 'nullable|exists:master_divisions,id',
            'jabatan' => 'required',
        ]);

        // Mapping jabatan ke level
        $levelMapping = [
            'Staff/Admin' => 1,
            'Koordinator' => 1.10,
            'Supervisor' => 2,
            'Junior Manajer' => 3,
            'Manajer' => 4,
            'Junior Manajer FAC' => 9,
            'Manajer FAC' => 9,
            'Direktur' => 9.99,
        ];

        $position = new MasterPosition();
        $position->name = $request->name;
        $position->description = $request->description;
        $position->department_id = $request->department_id ?: null;
        $position->division_id = $request->division_id ?: null;
        $position->jabatan = $request->jabatan;
        $position->level = $levelMapping[$request->jabatan];
        $position->save();

        return redirect()->route('positions.index')->with('success', 'Posisi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterPosition $position)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPosition $position)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPosition $position)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'department_id' => 'nullable|exists:master_departments,id',
            'division_id' => 'nullable|exists:master_divisions,id',
            'jabatan' => 'required',
        ]);

        // Mapping jabatan ke level
        $levelMapping = [
            'Staff/Admin' => 1,
            'Koordinator' => 1.10,
            'Supervisor' => 2,
            'Junior Manajer' => 3,
            'Manajer' => 4,
            'Junior Manajer FAC' => 9,
            'Manajer FAC' => 9,
            'Direktur' => 9.99,
        ];

        $position->name = $request->name;
        $position->description = $request->description;
        $position->department_id = $request->department_id ?: null;
        $position->division_id = $request->division_id ?: null;
        $position->jabatan = $request->jabatan;
        $position->level = $levelMapping[$request->jabatan];
        $position->save();

        return redirect()->route('positions.index')->with('success', 'Posisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPosition $position)
    {
        $position->delete();
        return redirect()->route('positions.index')->with('success', 'Posisi berhasil dihapus.');
    }
}
