<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('cash_sessions', function (Blueprint $table) {
            $table->id(); 
            
            $table->unsignedBigInteger('id_user'); 
            
            $table->decimal('opening_amount', 15, 2); 
            $table->decimal('closing_amount', 15, 2)->nullable(); 
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->json('payment_details')->nullable(); 
            $table->timestamps();

            // Relación manual apuntando a id_user
            $table->foreign('id_user')
                  ->references('id_user') // <--- Nombre de la llave en tu tabla users
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('cash_sessions');
    }
};