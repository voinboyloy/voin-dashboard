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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('notion_id')->nullable();
        });
        Schema::table('time_blocks', function (Blueprint $table) {
            $table->string('notion_id')->nullable();
        });
        Schema::table('daily_reviews', function (Blueprint $table) {
            $table->string('notion_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('notion_id');
        });
        Schema::table('time_blocks', function (Blueprint $table) {
            $table->dropColumn('notion_id');
        });
        Schema::table('daily_reviews', function (Blueprint $table) {
            $table->dropColumn('notion_id');
        });
    }
};
