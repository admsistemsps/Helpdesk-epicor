<?php

namespace App\Http\Controllers;

use App\Models\TicketHead;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\TicketDetail;
use App\Models\TicketApproval;
use App\Models\TicketAssign;
use Carbon\Carbon;

use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function generate(TicketHead $ticket)
    {
        Carbon::setLocale('id');
        $details = TicketDetail::where('ticket_head_id', $ticket->id)->orderBy('ticket_line')->get();

        $approve = TicketApproval::where('ticket_id', $ticket->id)
            ->where('action', 'Approved')
            ->orderBy('level')->first();

        $approvals = TicketApproval::where('ticket_id', $ticket->id)
            ->where('action', 'Approved')
            ->orderBy('level')->get();

        $assigned = TicketAssign::where('ticket_head_id', $ticket->id)->first();

        $judul = "FORM UBAH HAPUS DATABASE";

        $pdf = Pdf::loadView('pdf.form', compact(
            'ticket',
            'details',
            'approve',
            'approvals',
            'assigned',
            'judul'
        ));

        $pdf->setPaper('A5', 'landscape');

        $fileName = $ticket->slug . '.pdf';

        return $pdf->stream($fileName);
    }
}
