<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_permission');
            $table->unsignedBigInteger('id_role');

            $table->foreign('id_permission')
                ->references('id_permission')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('id_role')
                ->references('id_role')
                ->on('roles')
                ->onDelete('cascade');

            $table->unique(['id_permission', 'id_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
