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
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_profile_id')->constrained()->onDelete('cascade');
            $table->enum('priority', ['primary', 'secondary'])->default('primary');
            $table->string('name');
            $table->enum('relationship', ['family', 'friend', 'other'])->default('family');
            $table->string('phone_number');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
    }
};
