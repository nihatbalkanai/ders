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
        Schema::table('ai_providers', function (Blueprint $table) {
            $table->boolean('is_active_text')->default(false)->after('api_key');
            $table->boolean('is_active_image')->default(false)->after('is_active_text');
            $table->dropColumn('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_providers', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('api_key');
            $table->dropColumn(['is_active_text', 'is_active_image']);
        });
    }
};
