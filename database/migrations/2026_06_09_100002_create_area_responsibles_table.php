<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_responsibles', function (Blueprint $table) {
            $table->id();
            // area_id -> timeit.areas.id ; user_id -> timeit.usuarios.id (BD externa).
            $table->unsignedInteger('area_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->timestamps();

            $table->unique(['area_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_responsibles');
    }
};
