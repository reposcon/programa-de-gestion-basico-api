<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id_customer';

    protected $fillable = [
        'name_customer',
        'document_number_customer',
        'email_customer',
        'phone_customer',
        'state_customer'
    ];
}