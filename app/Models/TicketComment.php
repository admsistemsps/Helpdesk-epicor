<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    protected $table = 'ticket_comments';

    protected $fillable = [
        'ticket_head_id',
        'user_id',
        'action',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(TicketHead::class);
    }
}
