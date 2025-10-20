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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('concentration')->nullable();
            $table->text('indications')->nullable();
            $table->text('contraindications')->nullable();
            $table->foreignId('medication_type_id')->constrained('medication_types');
            $table->foreignId('medication_presentation_id')->constrained('medication_presentations');
            $table->foreignId('medication_administration_id')->constrained('medication_administrations');
            $table->foreignId('unit_id')->constrained('units');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
