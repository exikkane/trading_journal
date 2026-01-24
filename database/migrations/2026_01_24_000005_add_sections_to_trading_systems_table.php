<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trading_systems', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->text('traded_pairs')->nullable()->after('title');
            $table->text('analysis_tools')->nullable()->after('traded_pairs');
            $table->text('analysis_algorithm')->nullable()->after('analysis_tools');
            $table->text('risk_intro')->nullable()->after('analysis_algorithm');
            $table->text('risk_live')->nullable()->after('risk_intro');
            $table->text('risk_personal')->nullable()->after('risk_live');
            $table->text('risk_challenge')->nullable()->after('risk_personal');
            $table->text('risk_loss_reduction')->nullable()->after('risk_challenge');
            $table->text('risk_note')->nullable()->after('risk_loss_reduction');
            $table->text('risk_params')->nullable()->after('risk_note');
            $table->text('risk_limits')->nullable()->after('risk_params');
            $table->text('risk_footer')->nullable()->after('risk_limits');
        });
    }

    public function down(): void
    {
        Schema::table('trading_systems', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'traded_pairs',
                'analysis_tools',
                'analysis_algorithm',
                'risk_intro',
                'risk_live',
                'risk_personal',
                'risk_challenge',
                'risk_loss_reduction',
                'risk_note',
                'risk_params',
                'risk_limits',
                'risk_footer',
            ]);
        });
    }
};
