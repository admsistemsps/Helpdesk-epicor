<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketPriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $priorities = \App\Models\TicketPriority::all();

        return view('master.prioritas.index', compact('priorities'));
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
            'name' => 'required|unique:ticket_priorities,name',
            'sla_hours' => 'required|numeric'
        ]);

        $priority = new \App\Models\TicketPriority();
        $priority->name = $request->name;
        $priority->sla_hours = $request->sla_hours;
        $priority->save();

        return redirect()->route('priorities.index')->with('success', 'Prioritas berhasil ditambahkan.');
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'sla_hours' => 'required|numeric'
        ]);

        $priority = \App\Models\TicketPriority::find($id);
        $priority->name = $request->name;
        $priority->sla_hours = $request->sla_hours;
        $priority->save();

        return redirect()->route('priorities.index')->with('success', 'Prioritas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $priority = \App\Models\TicketPriority::find($id);
        $priority->delete();
        return redirect()->route('priorities.index')->with('success', 'Prioritas berhasil dihapus.');
    }
}
