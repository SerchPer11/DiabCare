<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plan_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // alimentacion, actividad_fisica
            $table->string('description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default plan types
        DB::table('plan_types')->insert([
            [
                'name' => 'alimentacion',
                'description' => 'Plan de alimentación',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'actividad_fisica',
                'description' => 'Plan de actividad física',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_types');
    }
};
