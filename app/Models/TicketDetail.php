<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketDetail extends Model
{
    protected $table = 'ticket_details';

    protected $fillable = [
        'ticket_head_id',
        'ticket_line',
        'nomor',
        'desc_before',
        'desc_after',
        'comment',
        'reason',
        'created_date',
        'closed_date',
        'status',
    ];

    public function ticketHead()
    {
        return $this->belongsTo(TicketHead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }
}
