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
        Schema::create('pairs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('category');
            $table->timestamps();
        });

        DB::table('pairs')->insertOrIgnore([
            ['name' => 'EUR/USD', 'category' => 'forex', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GBP/USD', 'category' => 'forex', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GER40', 'category' => 'indices', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pairs');
    }
};
