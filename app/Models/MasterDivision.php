<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterDivision extends Model
{
    use SoftDeletes;

    protected $table = 'master_divisions';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'code',
        'name',
        'department_id',
    ];

    public function department()
    {
        return $this->belongsTo(MasterDepartment::class);
    }

    public function positions()
    {
        return $this->hasMany(MasterPosition::class);
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
}
