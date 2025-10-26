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
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('food_group_id')->constrained('food_groups')->onDelete('cascade');
            $table->integer('calories')->nullable();
            $table->integer('protein')->nullable();
            $table->integer('carbohydrates')->nullable();
            $table->integer('fats')->nullable();
            $table->integer('fiber')->nullable();
            $table->text('description')->nullable();
            $table->integer('portion_size')->nullable();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
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
        Schema::dropIfExists('foods');
    }
};
