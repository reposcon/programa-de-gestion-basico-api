<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashSession extends Model
{
    use HasFactory;

    protected $table = 'cash_sessions';

    protected $fillable = [
        'id_user',
        'opening_amount',
        'closing_amount',
        'opened_at',
        'closed_at',
        'status',
        'payment_details'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'payment_details' => 'array', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}