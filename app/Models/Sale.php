<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $primaryKey = 'id_sale';

    protected $fillable = [
        'invoice_number',
        'subtotal',
        'total_tax',
        'total_sale',
        'uvt_value',
        'customer_id',
        'seller_id'
    ];

    // Relación con los items vendidos
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id', 'id_sale');
    }

    // Una venta tiene muchos registros de pago
    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class, 'sale_id', 'id_sale');
    }

    // Relación con el cliente
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id_customer');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id_user');
    }
}
