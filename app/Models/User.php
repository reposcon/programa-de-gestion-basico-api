<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'name_user',
        'password_user',
        'state_user',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [
        'password_user',
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_user', 
            'id_user',  
            'id_role'   
        );
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($q) use ($permission) {
                $q->where('name_permission', $permission);
            })
            ->exists();
    }

    public function getAuthPassword()
    {
        return $this->password_user;
    }
}
