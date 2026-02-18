<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    \App\Models\PaymentMethod::create(['name_payment_method' => 'Efectivo']);
    \App\Models\PaymentMethod::create(['name_payment_method' => 'Tarjeta']);
    \App\Models\PaymentMethod::create(['name_payment_method' => 'crediNalga']);
    \App\Models\PaymentMethod::create(['name_payment_method' => 'Nequi/Daviplata']);
}
}
