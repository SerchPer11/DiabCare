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
        Schema::table('plan_elements', function (Blueprint $table) {
            // Agregar campos adicionales para mejor control
            $table->string('time_schedule')->nullable()->after('instructions'); // horario/momento del día
            $table->text('notes')->nullable()->after('time_schedule'); // notas adicionales
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_elements', function (Blueprint $table) {
            $table->dropColumn(['time_schedule', 'notes']);
        });
    }
};
