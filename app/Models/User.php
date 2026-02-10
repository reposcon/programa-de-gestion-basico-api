<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'name_user',
        'password_user',
        'rol',
        'state',
    ];

    protected $hidden = [
        'password_user',
    ];

    // Sobrescribir método para que Laravel use password_user como contraseña
    public function getAuthPassword()
    {
        return $this->password_user;
    }
}
