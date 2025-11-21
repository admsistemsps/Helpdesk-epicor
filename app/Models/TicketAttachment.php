<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketAttachment extends Model
{
    use SoftDeletes;
    protected $table = 'ticket_attachments';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'file_name',
        'file_path',
        'ticket_head_id'
    ];

    public function ticket()
    {
        return $this->belongsTo(TicketHead::class);
    }
}
