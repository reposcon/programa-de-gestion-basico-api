<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $primaryKey = 'id_permission';

    protected $fillable = [
        'name_permission',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'permission_role',
            'id_permission',
            'id_role'
        );
    }
}
