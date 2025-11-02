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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_type_id')->constrained('plan_types');
            $table->foreignId('assigned_by')->constrained('users'); // Doctor/Admin que lo asigna
            $table->string('title')->nullable(); // Título del plan
            $table->date('start_date'); // Fecha de inicio
            $table->date('end_date'); // Fecha de fin
            $table->enum('status', ['activo', 'completado', 'cancelado'])->default('activo');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['patient_id', 'status']);
            $table->index(['plan_type_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
