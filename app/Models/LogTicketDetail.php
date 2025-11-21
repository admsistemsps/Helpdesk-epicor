<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogTicketDetail extends Model
{
    protected $fillable = [
        'ticket_detail_id',
        'ticket_head_id',
        'ticket_line',
        'nomor',
        'desc_before',
        'desc_after',
        'comment',
        'reason',
        'action',
        'logged_by',
        'logged_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    public function ticketHead()
    {
        return $this->belongsTo(TicketHead::class, 'ticket_head_id');
    }

    public function ticketDetail()
    {
        return $this->belongsTo(TicketDetail::class, 'ticket_detail_id');
    }
}
