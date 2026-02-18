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
        Schema::create('tax_settings', function (Blueprint $table) {
            $table->id('id_tax');
            $table->string('tax_name');
            $table->decimal('tax_rate', 5, 2);
            $table->enum('tax_type', ['IVA', 'INC', 'EXENTO', 'EXCLUIDO']);
            $table->boolean('state_tax')->default(1);
            $table->timestamps();
        });

        Schema::create('global_configs', function (Blueprint $table) {
            $table->id('id_config');
            $table->string('config_key')->unique();
            $table->decimal('config_value', 15, 2);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('global_configs');
        Schema::dropIfExists('tax_settings');
    }
};
