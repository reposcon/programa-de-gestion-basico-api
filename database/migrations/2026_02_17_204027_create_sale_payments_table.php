<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id('id_sale_payment');
            $table->foreignId('sale_id')->constrained('sales', 'id_sale')->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained('payment_methods', 'id_payment_method');
            $table->decimal('amount_paid', 15, 2);
            $table->decimal('change_returned', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
    }
};