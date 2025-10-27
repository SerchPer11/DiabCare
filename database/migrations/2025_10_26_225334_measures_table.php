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
        Schema::create('measures', function (Blueprint $table) {
            $table->id();
            $table->decimal('value', 7, 2)->nullable();
            $table->date('measured_at')->nullable();
            $table->time('hour_measured')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('measure_config_id')->constrained('measure_configs');
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
        Schema::dropIfExists('measures');
    }
};
