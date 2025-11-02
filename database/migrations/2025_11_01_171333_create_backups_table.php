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
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename'); // Nombre del archivo de respaldo
            $table->string('path'); // Ruta donde se almacena el archivo
            $table->bigInteger('size')->nullable(); // Tamaño del archivo en bytes
            $table->string('checksum')->nullable(); // Hash para verificar integridad
            $table->enum('status', ['pending', 'completed', 'failed', 'corrupted'])->default('pending');
            $table->text('description')->nullable(); // Descripción opcional del respaldo
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Usuario que creó el respaldo
            $table->timestamp('completed_at')->nullable(); // Cuando se completó el respaldo
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
