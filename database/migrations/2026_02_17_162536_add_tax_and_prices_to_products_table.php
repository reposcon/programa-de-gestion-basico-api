<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'price_cost')) {
                $table->decimal('price_cost', 15, 2)->default(0)->after('subcategory_id');
            }
            if (!Schema::hasColumn('products', 'price_net')) {
                $table->decimal('price_net', 15, 2)->default(0)->after('price_cost');
            }
            if (!Schema::hasColumn('products', 'tax_id')) {
                $table->unsignedBigInteger('tax_id')->nullable()->after('price_net');
            }
            if (!Schema::hasColumn('products', 'price_sell')) {
                $table->decimal('price_sell', 15, 2)->default(0)->after('tax_id');
            }
            if (!Schema::hasColumn('products', 'is_tax_included')) {
                $table->boolean('is_tax_included')->default(false)->after('price_sell');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('tax_id')
                  ->references('id_tax')
                  ->on('tax_settings')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Es vital usar el nombre del índice o el array para borrar la FK
            $table->dropForeign(['tax_id']);
            $table->dropColumn(['price_cost', 'price_net', 'tax_id', 'price_sell', 'is_tax_included']);
        });
    }
};