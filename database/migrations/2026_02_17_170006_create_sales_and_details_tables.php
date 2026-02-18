<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Maestro de Ventas
        Schema::create('sales', function (Blueprint $table) {
            $table->id('id_sale');
            $table->string('invoice_number')->unique();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('total_tax', 15, 2);
            $table->decimal('total_sale', 15, 2);

            // Datos para cumplimiento legal Colombia 2026
            $table->decimal('uvt_value', 10, 2); 
            $table->unsignedBigInteger('customer_id')->nullable(); // Obligatorio si > 5 UVT 

            $table->unsignedBigInteger('seller_id');
            $table->foreign('seller_id')->references('id_user')->on('users');

            $table->timestamps();
        });

        // Detalle de la Venta (Lo que se lleva el cliente)
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('price_at_sale', 15, 2); 
            $table->decimal('tax_rate_at_sale', 5, 2); 
            $table->decimal('tax_amount', 15, 2);
            $table->decimal('total_item', 15, 2);
            $table->timestamps();

            
            $table->foreign('sale_id')->references('id_sale')->on('sales')->onDelete('cascade');
            $table->foreign('product_id')->references('id_product')->on('products');
        });
    }
};
