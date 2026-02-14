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
        // No incluyas id_role aquí si usas la tabla intermedia
    ];

    protected $hidden = [
        'password_user',
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_user', // Tu tabla intermedia
            'id_user',   // FK en role_user que apunta a users
            'id_role'    // FK en role_user que apunta a roles
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