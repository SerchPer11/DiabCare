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
        Schema::create('clinical_log_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            
            // Tipo de evento
            $table->enum('event_type', ['observation', 'medication_adjustment', 'incident', 'document'])
                  ->default('observation');
            
            // Información principal
            $table->string('title', 255);
            $table->text('description');
            $table->text('notes')->nullable();
            
            // Fecha y hora del evento
            $table->timestamp('event_datetime');
            
            // Relación polimórfica opcional (cita, plan, medicamento)
            $table->nullableMorphs('related');
            
            // Estado del evento
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para mejorar rendimiento
            $table->index(['patient_id', 'event_type']);
            $table->index(['doctor_id', 'created_at']);
            $table->index(['event_datetime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_log_entries');
    }
};
