<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterMenu extends Model
{
    use SoftDeletes;

    protected $table = 'master_menus';

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'description',
        'division_id',
        'department_id',
    ];

    public function roles()
    {
        return $this->belongsToMany(MasterRole::class);
    }

    public function approvalRules()
    {
        return $this->hasMany(TicketApprovalRule::class);
    }

    public function tickets()
    {
        return $this->hasMany(TicketHead::class);
    }

    public function subMenus()
    {
        return $this->hasMany(MasterSubmenu::class);
    }

    public function ticketApprovals()
    {
        return $this->hasMany(TicketApproval::class);
    }

    public function divisions()
    {
        return $this->belongsToMany(MasterDivision::class, 'menu_division', 'menu_id', 'division_id');
    }

    public function departments()
    {
        return $this->belongsToMany(MasterDepartment::class, 'menu_department', 'menu_id', 'department_id');
    }
}
