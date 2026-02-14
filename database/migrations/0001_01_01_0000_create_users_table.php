<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id('id_user');
        $table->string('name_user');
        $table->string('password_user');
        $table->boolean('state_user')->default(true);
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('users'); //
    }
};
