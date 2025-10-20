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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('exercise_type_id')->constrained()->cascadeOnDelete();
            $table->enum('intensity', ['baja', 'media', 'alta'])->default('baja');
            $table->integer('duration_minutes');
            $table->text('description')->nullable();
            $table->integer('calories_burned');
            $table->integer('sets');
            $table->integer('repetitions');
            $table->integer('rest_seconds');
            $table->string('equipment')->nullable();
            $table->text('contraindications')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
