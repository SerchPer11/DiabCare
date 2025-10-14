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
            $table->enum('diabetes', ['yes', 'no'])->default('no');
            $table->enum('diabetes_type', ['TP1', 'TP2', 'Gest'])->nullable(); // Tipo 1, Tipo 2, Gestacional
            $table->date('diabetes_diagnosis_date')->nullable();
            $table->enum('hypertension', ['yes', 'no'])->default('no');
            $table->date('hypertension_diagnosis_date')->nullable();
            $table->enum('obesity', ['yes', 'no'])->default('no');
            $table->enum('obesity_type', ['no', 'I', 'II', 'III'])->default('no'); // Grados de obesidad
            $table->enum('allergies', ['yes', 'no'])->default('no');
            $table->text('allergy_details')->nullable();
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
