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
        'role_id',
        'state_user',
    ];

    protected $hidden = [
        'password_user',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id_role');
    }

    public function getAuthPassword()
    {
        return $this->password_user;
    }
}