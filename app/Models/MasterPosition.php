<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPosition extends Model
{
    use SoftDeletes;

    protected $table = 'master_positions';

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'description',
        'jabatan',
        'master_division_id',
        'level',
        'master_department_id',
    ];

    public function division()
    {
        return $this->belongsTo(MasterDivision::class, 'master_division_id');
    }

    public function role()
    {
        return $this->belongsTo(MasterRole::class, 'role_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'position_user', 'position_id', 'user_id');
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

    public function department()
    {
        return $this->belongsTo(MasterDepartment::class, 'master_department_id');
    }
}
