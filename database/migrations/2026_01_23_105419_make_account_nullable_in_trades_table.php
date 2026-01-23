<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            // Intentionally empty: using raw SQL to avoid doctrine/dbal dependency.
        });

        DB::statement('ALTER TABLE trades MODIFY account VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            // Intentionally empty: using raw SQL to avoid doctrine/dbal dependency.
        });

        DB::statement("UPDATE trades SET account = '' WHERE account IS NULL");
        DB::statement('ALTER TABLE trades MODIFY account VARCHAR(255) NOT NULL');
    }
};
