<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalConfig extends Model
{
    use HasFactory;

    protected $table = 'global_configs';
    protected $primaryKey = 'id_config';

    protected $fillable = [
        'config_key',
        'config_value',
        'description'
    ];
}