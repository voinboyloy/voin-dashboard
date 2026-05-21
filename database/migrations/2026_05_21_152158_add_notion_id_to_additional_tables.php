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
        $tables = [
            'transactions',
            'wishlist_items',
            'subscriptions',
            'habits',
            'workout_plans',
            'exercises',
            'credentials',
            'notes',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableBlueprint) {
                $tableBlueprint->string('notion_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'transactions',
            'wishlist_items',
            'subscriptions',
            'habits',
            'workout_plans',
            'exercises',
            'credentials',
            'notes',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableBlueprint) {
                $tableBlueprint->dropColumn('notion_id');
            });
        }
    }
};
