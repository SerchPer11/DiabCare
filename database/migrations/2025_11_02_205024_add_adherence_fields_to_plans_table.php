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
        Schema::table('plans', function (Blueprint $table) {
            // Campos de adherencia
            $table->decimal('overall_adherence', 5, 2)->default(0)->after('status')
                ->comment('Adherencia general del plan en porcentaje (0-100)');
            $table->integer('days_tracked')->default(0)->after('overall_adherence')
                ->comment('Número total de días con seguimiento registrado');
            $table->date('last_tracked_date')->nullable()->after('days_tracked')
                ->comment('Última fecha en que se registró seguimiento');
            $table->integer('total_plan_days')->default(0)->after('last_tracked_date')
                ->comment('Total de días que dura el plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'overall_adherence',
                'days_tracked', 
                'last_tracked_date',
                'total_plan_days'
            ]);
        });
    }
};
