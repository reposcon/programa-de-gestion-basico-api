<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            // ID principal personalizado
            $table->id('id_product');
            
            // Atributos del producto
            $table->string('name_product');
            
            /* Mantenemos boolean ya que en MySQL se traduce a TINYINT(1), 
               que es lo más eficiente para estados (0 o 1).
            */
            $table->boolean('state_product')->default(true);

            // Llaves foráneas (Relaciones)
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('subcategory_id');

            // Definición de las restricciones de integridad
            $table->foreign('category_id')
                  ->references('id_category')
                  ->on('categories')
                  ->onDelete('cascade');

            $table->foreign('subcategory_id')
                  ->references('id_subcategory')
                  ->on('subcategories')
                  ->onDelete('cascade');

            // Fecha de creación y actualización
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};