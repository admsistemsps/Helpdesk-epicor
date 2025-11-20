<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAssign extends Model
{
    protected $table = 'ticket_assigns';

    protected $fillable = [
        'ticket_head_id',
        'action',
        'comment',
        'priority_id',
        'assigned_by',
        'assigned_to',
        'is_consultant',
        'assigned_date',
        'started_date',
        'due_date',
        'due_time',
        'completed_date',
    ];

    protected $dates = ['assigned_date', 'started_date', 'completed_date'];
    protected $casts = [
        'started_date' => 'datetime',
        'assigned_date' => 'datetime',
        'completed_date' => 'datetime',
    ];

    public function ticketHead()
    {
        return $this->belongsTo(TicketHead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function priority()
    {
        return $this->belongsTo(TicketPriority::class);
    }
}
