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
        Schema::table('trades', function (Blueprint $table) {
            if (Schema::hasColumn('trades', 'parent_trade_id')) {
                $table->dropConstrainedForeignId('parent_trade_id');
            }
            if (Schema::hasColumn('trades', 'account_id')) {
                $table->dropConstrainedForeignId('account_id');
            }
            if (Schema::hasColumn('trades', 'account')) {
                $table->dropColumn('account');
            }
            if (Schema::hasColumn('trades', 'risk_reward')) {
                $table->dropColumn('risk_reward');
            }
            if (Schema::hasColumn('trades', 'risk_pct')) {
                $table->dropColumn('risk_pct');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            if (! Schema::hasColumn('trades', 'account')) {
                $table->string('account')->nullable();
            }
            if (! Schema::hasColumn('trades', 'account_id')) {
                $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('trades', 'parent_trade_id')) {
                $table->foreignId('parent_trade_id')->nullable()->constrained('trades')->nullOnDelete();
            }
            if (! Schema::hasColumn('trades', 'risk_reward')) {
                $table->decimal('risk_reward', 6, 2)->default(0);
            }
            if (! Schema::hasColumn('trades', 'risk_pct')) {
                $table->decimal('risk_pct', 5, 2)->default(0);
            }
        });
    }
};
