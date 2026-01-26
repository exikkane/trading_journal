<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trading_plans', function (Blueprint $table) {
            $table->string('dxy_chart_screenshot_path')->nullable()->after('daily_chart_notes');
            $table->text('dxy_chart_notes')->nullable()->after('dxy_chart_screenshot_path');
        });
    }

    public function down(): void
    {
        Schema::table('trading_plans', function (Blueprint $table) {
            $table->dropColumn(['dxy_chart_screenshot_path', 'dxy_chart_notes']);
        });
    }
};
