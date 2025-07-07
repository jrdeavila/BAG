<?php

use App\Enums\ActivityPriority;
use App\Enums\ActivityStatus;
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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->integer('user_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->date('date');
            $table->enum('status', array_map(fn($status) => $status->value, ActivityStatus::cases()))->default('pending');
            $table->enum('priority', array_map(fn($priority) => $priority->value, ActivityPriority::cases()))->default('low');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on(env('DB_TIMEIT_DATABASE') . '.usuarios')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('created_by')->references('id')->on(env('DB_TIMEIT_DATABASE') . '.usuarios')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
