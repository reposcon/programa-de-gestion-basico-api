<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_role');
            $table->unsignedBigInteger('id_user');

            $table->foreign('id_role')
                ->references('id_role')
                ->on('roles')
                ->onDelete('cascade');

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade');

            $table->unique(['id_role', 'id_user']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
