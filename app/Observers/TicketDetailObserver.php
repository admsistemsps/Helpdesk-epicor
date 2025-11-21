<?php

namespace App\Observers;

use App\Models\TicketDetail;
use App\Models\LogTicketHead;
use Illuminate\Support\Facades\Auth;

class TicketDetailObserver
{
    public static bool $suppressLogging = false;

    public function created(TicketDetail $detail)
    {
        if (self::$suppressLogging) return;
        LogTicketHead::create([
            'ticket_head_id' => $detail->ticket_head_id,
            'user_id' => Auth::id(),
            'action' => 'Detail added',
            'remark' => 'Detail baru ditambahkan oleh ' . (Auth::user()->name ?? 'System'),
        ]);
    }

    public function deleted(TicketDetail $detail)
    {
        if (self::$suppressLogging) return;
        LogTicketHead::create([
            'ticket_head_id' => $detail->ticket_head_id,
            'user_id' => Auth::id(),
            'action' => 'Detail deleted',
            'remark' => 'Detail dihapus oleh ' . (Auth::user()->name ?? 'System'),
        ]);
    }
}
