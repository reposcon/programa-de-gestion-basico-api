<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    protected $table = 'sale_payments';
    protected $primaryKey = 'id_sale_payment';

    protected $fillable = [
        'sale_id',
        'payment_method_id',
        'amount_paid',
        'change_returned'
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id_sale');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id_payment_method');
    }
}