<?php

namespace App\Http\Controllers;

use App\Models\MasterSite;
use Illuminate\Http\Request;

class MasterSiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $masterSites = MasterSite::all();

        return view('master.site.index', compact('masterSites'));
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
            'code' => 'required|unique:master_sites,code',
            'name' => 'required|unique:master_sites,name',
            'address' => 'required',
        ]);

        $masterSite = MasterSite::create([
            'code' => $request->code,
            'name' => $request->name,
            'address' => $request->address
        ]);

        return redirect()->route('sites.index')->with('success', 'Site berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterSite $masterSite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterSite $masterSite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterSite $masterSite)
    {
        $request->validate([
            'code' => 'required|unique:master_sites,code,' . $masterSite->id,
            'name' => 'required|unique:master_sites,name,' . $masterSite->id,
            'address' => 'required',
        ]);

        $masterSite->update([
            'code' => $request->code,
            'name' => $request->name,
            'address' => $request->address
        ]);

        return redirect()->route('sites.index')->with('success', 'Site berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterSite $masterSite)
    {
        $masterSite->delete();
        return redirect()->route('sites.index')->with('success', 'Site berhasil dihapus.');
    }
}
