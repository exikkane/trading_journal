<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('trades', 'account_id')) {
            return;
        }
        if (! Schema::hasColumn('trades', 'parent_trade_id')) {
            return;
        }
        if (! Schema::hasColumn('trades', 'risk_reward') || ! Schema::hasColumn('trades', 'risk_pct')) {
            return;
        }

        DB::statement('INSERT INTO account_trades (trade_id, account_id, risk_reward, risk_pct, created_at, updated_at)
            SELECT parent_trade_id, account_id, COALESCE(risk_reward, 0), COALESCE(risk_pct, 0), created_at, updated_at
            FROM trades
            WHERE parent_trade_id IS NOT NULL AND account_id IS NOT NULL');

        DB::statement('INSERT INTO account_trades (trade_id, account_id, risk_reward, risk_pct, created_at, updated_at)
            SELECT id, account_id, COALESCE(risk_reward, 0), COALESCE(risk_pct, 0), created_at, updated_at
            FROM trades
            WHERE parent_trade_id IS NULL
              AND account_id IS NOT NULL
              AND id NOT IN (SELECT DISTINCT parent_trade_id FROM trades WHERE parent_trade_id IS NOT NULL)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DELETE FROM account_trades');
    }
};
