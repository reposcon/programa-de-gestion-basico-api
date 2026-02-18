<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';
    protected $primaryKey = 'id_payment_method';

    protected $fillable = [
        'name_payment_method',
        'state_payment_method'
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class, 'payment_method_id', 'id_payment_method');
    }
}