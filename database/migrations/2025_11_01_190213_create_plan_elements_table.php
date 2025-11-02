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
        Schema::create('plan_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            
            // Para planes de alimentación
            $table->foreignId('food_id')->nullable()->constrained('foods')->onDelete('cascade');
            
            // Para planes de actividad física  
            $table->foreignId('exercise_id')->nullable()->constrained('exercises')->onDelete('cascade');
            
            // Campos comunes
            $table->string('frequency'); // diaria, semanal, quincenal, etc.
            $table->string('intensity')->nullable(); // baja, media, alta (para ejercicios)
            $table->decimal('quantity', 8, 2)->nullable(); // cantidad/porción
            $table->string('unit')->nullable(); // gramos, piezas, minutos, etc.
            $table->text('instructions')->nullable(); // instrucciones específicas
            $table->integer('order')->default(0); // orden de presentación
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['plan_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_elements');
    }
};
