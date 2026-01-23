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
        // No-op: legacy columns are dropped in a later migration after backfill.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->string('account')->nullable();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_trade_id')->nullable()->constrained('trades')->nullOnDelete();
            $table->decimal('risk_reward', 6, 2)->default(0);
            $table->decimal('risk_pct', 5, 2)->default(0);
        });
    }
};
