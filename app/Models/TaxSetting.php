<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    use HasFactory;

    protected $table = 'tax_settings';
    protected $primaryKey = 'id_tax';

    protected $fillable = [
        'tax_name',
        'tax_rate',
        'tax_type',
        'state_tax'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'tax_id', 'id_tax');
    }
}