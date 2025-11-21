<?php

namespace App\Observers;

use App\Models\TicketHead;
use App\Models\LogTicketHead;
use Illuminate\Support\Facades\Auth;

class TicketHeadObserver
{
    public function created(TicketHead $ticket): void
    {
        LogTicketHead::create([
            'ticket_head_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'Ticket created',
            'remark' => 'Tiket baru dibuat oleh ' . (Auth::user()->name ?? 'System'),
        ]);
    }

    public function updated(TicketHead $ticket): void
    {
        $changes = $ticket->getChanges();
        $user = Auth::user();

        if (isset($changes['status'])) {
            $status = $changes['status'];
            $remark = '';

            switch ($status) {
                case 'Completed':
                    $lastComment = $ticket->comments()->latest()->first();
                    $commentText = $lastComment?->comment ?? '-';
                    $remark = 'Ticket diselesaikan oleh ' . ($user->name ?? 'System') .
                        ' dengan komentar: ' . ($commentText ?? '-');
                    break;

                case 'Feedback':
                    $lastComment = $ticket->comments()->latest()->first();
                    $commentText = $lastComment?->comment ?? '-';
                    $remark = 'Feedback diberikan oleh ' . ($user->name ?? 'System') .
                        ' dengan isi: ' . ($commentText ?? '-');
                    break;

                case 'Closed':
                    $remark = 'Ticket ditutup oleh ' . ($user->name ?? 'System');
                    break;

                case 'Waiting_assignment':
                    $remark = 'Ticket diassign ke ' . ($ticket->assignUser->name ?? 'User Tidak Diketahui');
                    break;

                case 'Approved':
                    $remark = 'Ticket disetujui oleh ' . ($user->name ?? 'System');
                    break;

                case 'Rejected':
                    $lastComment = $ticket->comments()->latest()->first();
                    $commentText = $lastComment?->comment ?? '-';
                    $remark = 'Ticket ditolak oleh ' . ($user->name ?? 'System') .
                        ' dengan komentar: ' . $commentText;
                    break;

                default:
                    $remark = "Status berubah menjadi {$status}";
                    break;
            }

            LogTicketHead::create([
                'ticket_head_id' => $ticket->id,
                'user_id' => $user->id ?? null,
                'action' => 'Status Updated',
                'remark' => $remark,
            ]);
        }

        $fieldChanges = [];
        foreach ($changes as $field => $value) {
            if (!in_array($field, [
                'updated_at',
                'status',
                'action_comment',
                'feedback',
                'closed_date',
                'start_date',
                'finish_date',
                'assigned_to',
                'current_approval_level',
                'current_approval_value',
                'current_approval_position_id',
                'current_approval_division_id'
            ])) {
                $fieldChanges[] = "$field changed to '$value'";
            }
        }

        if (!empty($fieldChanges)) {
            LogTicketHead::create([
                'ticket_head_id' => $ticket->id,
                'user_id' => $user->id ?? null,
                'action' => 'Ticket updated',
                'remark' => implode(', ', $fieldChanges),
            ]);
        }
    }

    public function deleted(TicketHead $ticket): void
    {
        LogTicketHead::create([
            'ticket_head_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'Ticket deleted',
            'remark' => 'Tiket dihapus oleh ' . (Auth::user()->name ?? 'System'),
        ]);
    }

    public function restored(TicketHead $ticket): void
    {
        LogTicketHead::create([
            'ticket_head_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'Ticket restored',
            'remark' => 'Tiket dikembalikan dari soft delete',
        ]);
    }

    public function forceDeleted(TicketHead $ticket): void
    {
        //
    }
}
