<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterSubMenu extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'master_sub_menus';
    protected $fillable = [
        'name',
        'description',
        'placeholder',
        'menu_id',
    ];

    public function menu()
    {
        return $this->belongsTo(MasterMenu::class);
    }

    public function tickets()
    {
        return $this->hasMany(TicketHead::class);
    }

    public function ticketApprovals()
    {
        return $this->hasMany(TicketApproval::class);
    }

    public function approvalRules()
    {
        return $this->hasMany(TicketApprovalRule::class);
    }
}
