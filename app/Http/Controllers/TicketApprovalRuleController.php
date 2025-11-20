<?php

namespace App\Http\Controllers;

use App\Models\TicketApprovalRule;
use Illuminate\Http\Request;

class TicketApprovalRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TicketApprovalRule $ticketApprovalRule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TicketApprovalRule $ticketApprovalRule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TicketApprovalRule $ticketApprovalRule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketApprovalRule $ticketApprovalRule)
    {
        $rule = TicketApprovalRule::find($ticketApprovalRule->id);

        if (!$rule) {
            return redirect()->back()->with('error', 'Approval rule tidak ditemukan.');
        }

        $rule->delete();

        return redirect()->back()->with('success', 'Approval rule berhasil dihapus.');
    }
}
