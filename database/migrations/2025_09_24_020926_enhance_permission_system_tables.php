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
        // Mejorar tabla permissions
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('description')->nullable()->after('guard_name');
            $table->string('module_key')->nullable()->after('description');
        });

        // Mejorar tabla roles  
        Schema::table('roles', function (Blueprint $table) {
            $table->string('description')->nullable()->after('guard_name');
        });

        // Mejorar tabla modules
        Schema::table('modules', function (Blueprint $table) {
            $table->string('key')->unique()->after('description');
            $table->foreignId('user_id')->constrained()->after('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['description', 'module_key']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['key', 'user_id']);
        });
    }
};
