<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketApprovalRule extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'role_id',
        'menu_id',
        'sub_menu_id',
        'position_id',
        'division_id',
        'level',
        'sequence',
        'is_mandatory',
        'is_final',
    ];

    protected $casts = [
        'level' => 'float',
        'sequence' => 'float',
        'is_final' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(MasterRole::class);
    }

    public function menu()
    {
        return $this->belongsTo(MasterMenu::class);
    }

    public function ticketApprovals()
    {
        return $this->hasMany(TicketApproval::class);
    }

    public function tickets()
    {
        return $this->hasMany(TicketHead::class);
    }

    public function subMenu()
    {
        return $this->belongsTo(MasterSubmenu::class);
    }

    public function department()
    {
        return $this->belongsTo(MasterDepartment::class);
    }

    public function division()
    {
        return $this->belongsTo(MasterDivision::class);
    }

    public function position()
    {
        return $this->belongsTo(MasterPosition::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEffectiveSequenceAttribute()
    {
        // kalau sequence ada pakai itu, kalau tidak pakai level
        return $this->sequence ?? (float) $this->level;
    }
}
