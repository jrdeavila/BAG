<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enabled_areas', function (Blueprint $table) {
            $table->id();
            // Referencia a timeit.areas.id (BD externa de solo lectura). La presencia de
            // la fila significa que el area esta habilitada para usar la plataforma.
            $table->unsignedInteger('area_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enabled_areas');
    }
};
