<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterRole extends Model
{
    use SoftDeletes;



    protected $table = 'master_roles';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'description',
        'level',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }

    public function departments()
    {
        return $this->belongsToMany(MasterDepartment::class, 'department_role', 'role_id', 'department_id');
    }

    public function positions()
    {
        return $this->hasMany(MasterPosition::class);
    }
}
