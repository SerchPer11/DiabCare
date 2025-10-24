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
        Schema::create('patient_pathologies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_profile_id')->constrained()->onDelete('cascade');
            $table->boolean('diabetes')->default(false);
            $table->enum('diabetes_type', ['TP1', 'TP2', 'Gest'])->nullable(); // Tipo 1, Tipo 2, Gestacional
            $table->date('diabetes_diagnosis_date')->nullable();
            $table->boolean('hypertension')->default(false);
            $table->enum('hypertension_type', ['I', 'II', 'III'])->nullable(); // Grados de hipertensión
            $table->date('hypertension_diagnosis_date')->nullable();
            $table->boolean('obesity')->default(false);
            $table->enum('obesity_type', ['N', 'I', 'II', 'III'])->nullable(); // Grados de obesidad
            $table->boolean('allergies')->default(false);
            $table->text('allergies_details')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_pathologies');
    }
};
