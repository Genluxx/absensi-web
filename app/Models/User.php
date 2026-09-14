<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'role_id',
        'area',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function karyawan()
    {
        return $this->hasMany(\App\Models\Karyawan::class, 'mandor_id');
    }
    public function roleData()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }
    
    public function hasPermission(string $slug): bool
    {
        // Super admin selalu punya semua akses
        if ($this->role === 'super_admin') {
            return true;
        }
    
        return $this->roleData && $this->roleData->hasPermission($slug);
    }
    public function presensi()
    {
        return $this->hasMany(\App\Models\Presensi::class, 'mandor_id');
    }
}

