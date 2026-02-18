<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    protected $table = 'tax_settings';
    protected $primaryKey = 'id_tax';

    protected $fillable = [
        'tax_name',
        'tax_rate',
        'tax_type',
        'state_tax'
    ];
}