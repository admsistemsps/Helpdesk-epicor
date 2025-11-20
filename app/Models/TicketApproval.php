<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketApproval extends Model
{
    use SoftDeletes;

    protected $table = 'ticket_approvals';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'position_id',
        'division_id',
        'level',
        'action',
        'note',
        'approved_at',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'approved_at',
        'updated_at',
        'deleted_at',
    ];

    public function ticket()
    {
        return $this->belongsTo(TicketHead::class, 'ticket_id', 'id');
    }

    public function approvalRule()
    {
        return $this->belongsTo(TicketApprovalRule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
