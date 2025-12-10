<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $dates = ['deleted_at'];
    protected $table = 'users';

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'profile_photo_path',
        'status',
        'master_role_id',
        'master_position_id',
        'master_division_id',
        'master_department_id',
        'master_site_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(MasterRole::class, 'master_role_id');
    }
    public function position()
    {
        return $this->belongsTo(MasterPosition::class, 'master_position_id');
    }
    public function division()
    {
        return $this->belongsTo(MasterDivision::class, 'master_division_id');
    }
    public function department()
    {
        return $this->belongsTo(MasterDepartment::class, 'master_department_id');
    }
    public function site()
    {
        return $this->belongsTo(MasterSite::class, 'master_site_id');
    }

    /** Helper */
    public function isSuperAdmin(): bool
    {
        $superNames = ['Admin Sistem', 'Manager IT', 'IT DEV'];
        return in_array(optional($this->role)->name, $superNames)
            || (bool)($this->is_super_admin ?? false);
    }
}
