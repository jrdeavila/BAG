<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_users', function (Blueprint $table) {
            $table->id();
            // user_id -> timeit.usuarios.id. Funcionario con acceso aunque su area
            // no este habilitada.
            $table->unsignedInteger('user_id')->unique();
            $table->string('reason', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_users');
    }
};
