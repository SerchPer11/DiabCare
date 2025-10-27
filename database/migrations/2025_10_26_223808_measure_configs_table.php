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
        Schema::create('measure_configs', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_value', 7, 2)->nullable();
            $table->decimal('max_value', 7, 2)->nullable();
            $table->enum('range', ['outrange', 'above', 'below'])->nullable();
            $table->enum('severity', ['low', 'medium', 'high'])->nullable();
            $table->enum('frequency', ['once', 'daily', 'weekly', 'monthly'])->nullable();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('measure_type_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('measure_configs');
    }
};
