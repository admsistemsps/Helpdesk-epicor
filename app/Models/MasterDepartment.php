<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterDepartment extends Model
{
    use SoftDeletes;

    protected $table = 'master_departments';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function roles()
    {
        return $this->belongsToMany(MasterRole::class, 'department_role', 'department_id', 'role_id');
    }

    public function divisions()
    {
        return $this->hasMany(MasterDivision::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function menus()
    {
        return $this->belongsToMany(MasterMenu::class);
    }

    public function subMenus()
    {
        return $this->belongsToMany(MasterSubmenu::class);
    }

    public function tickets()
    {
        return $this->belongsToMany(TicketHead::class);
    }

    public function ticketApprovals()
    {
        return $this->belongsToMany(TicketApproval::class);
    }

    public function positions()
    {
        return $this->hasMany(MasterPosition::class);
    }
}
