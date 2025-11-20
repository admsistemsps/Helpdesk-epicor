<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSite extends Model
{
    protected $table = 'master_sites';

    protected $fillable = [
        'code',
        'name',
        'address',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
