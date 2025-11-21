<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogTicketHead extends Model
{
    protected $fillable = [
        'ticket_head_id',
        'user_id',
        'action',
        'remark',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
