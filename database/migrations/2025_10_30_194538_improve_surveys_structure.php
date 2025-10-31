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
        Schema::table('surveys', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->text('instructions')->nullable();
        });

        Schema::table('survey_questions', function (Blueprint $table) {
            $table->unsignedTinyInteger('order')->default(1);
            $table->boolean('is_required')->default(true);
        });
        
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_complete')->default(false);
            $table->unique(['survey_id', 'user_id']); // Un usuario solo puede responder una vez
        });

        Schema::table('survey_answers', function (Blueprint $table) {
            $table->text('comment')->nullable(); // Para comentarios adicionales si se requiere
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['is_active', 'created_by', 'starts_at', 'ends_at', 'instructions']);
        });

        Schema::table('survey_questions', function (Blueprint $table) {
            $table->dropColumn(['order', 'is_required']);
        });

        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropUnique(['survey_id', 'user_id']);
            $table->dropColumn(['completed_at', 'is_complete']);
        });

        Schema::table('survey_answers', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
};
