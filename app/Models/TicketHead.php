<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TicketHead extends Model
{
    protected $table = 'ticket_heads';
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'site_id',
        'nomor_fuhd',
        'slug',
        'reason',
        'menu_id',
        'sub_menu_id',
        'requestor_id',
        'assigned_to',
        'status',
        'note_reject',
        'feedback',
        'action_comment',
        'current_approval_level',
        'created_date',
        'finish_date',
        'start_date',
        'closed_date',
        'current_approval_value',
        'current_approval_position_id',
        'current_approval_division_id',
    ];

    protected $casts = [
        'current_approval_value' => 'float',
        'current_approval_level' => 'float',
        'created_date' => 'datetime',
        'finish_date' => 'datetime',
        'start_date' => 'datetime',
        'closed_date' => 'datetime',
    ];

    protected static function booted()
    {
        // Saat tiket dibuat → generate slug dari nomor_fuhd
        static::creating(function ($ticket) {
            if (empty($ticket->slug)) {
                // Ganti '/' jadi '-' agar struktur nomor tetap terbaca
                $slugBase = strtolower(str_replace('/', '-', $ticket->nomor_fuhd));
                // Hilangkan karakter selain huruf, angka, dan '-'
                $slugBase = preg_replace('/[^a-z0-9\-]+/', '', $slugBase);
                // Pastikan unik
                $count = static::where('slug', 'like', "{$slugBase}%")->count();
                $ticket->slug = $count ? "{$slugBase}-{$count}" : $slugBase;
            }
        });

        // Jika nomor_fuhd diubah → perbarui slug juga
        static::updating(function ($ticket) {
            if ($ticket->isDirty('nomor_fuhd')) {
                $slugBase = strtolower(str_replace('/', '-', $ticket->nomor_fuhd));
                $slugBase = preg_replace('/[^a-z0-9\-]+/', '', $slugBase);
                $count = static::where('slug', 'like', "{$slugBase}%")->count();
                $ticket->slug = $count ? "{$slugBase}-{$count}" : $slugBase;
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'requestor_id');
    }

    public function menu()
    {
        return $this->belongsTo(MasterMenu::class);
    }

    public function ticketApprovals()
    {
        return $this->hasMany(TicketApproval::class, 'ticket_id', 'id');
    }

    public function approvedTicket()
    {
        return $this->hasOne(TicketApproval::class, 'ticket_id')
            ->where('action', 'Approved')
            ->latest('approved_at');
    }

    public function approvalRules()
    {
        return $this->hasMany(TicketApprovalRule::class);
    }

    public function division()
    {
        return $this->belongsTo(MasterDivision::class);
    }

    public function position()
    {
        return $this->belongsTo(MasterPosition::class);
    }

    public function subMenu()
    {
        return $this->belongsTo(MasterSubmenu::class);
    }

    public function department()
    {
        return $this->belongsTo(MasterDepartment::class);
    }

    public function assignUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getApprovedDateAttribute()
    {
        $approval = $this->ticketApprovals->firstWhere('level', 9.00);
        return $approval?->approved_at;
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'ticket_head_id');
    }

    public function details()
    {
        return $this->hasMany(TicketDetail::class, 'ticket_head_id')
            ->whereNull('deleted_at');;
    }

    public function logs()
    {
        return $this->hasMany(LogTicketHead::class, 'ticket_head_id');
    }

    public function ticketAssigns()
    {
        return $this->hasMany(TicketAssign::class, 'ticket_head_id');
    }
}
