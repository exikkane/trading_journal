<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trading_plans', function (Blueprint $table) {
            $table->id();
            $table->date('plan_date');
            $table->string('pair');
            $table->string('narrative', 20);
            $table->string('weekly_chart_screenshot_path')->nullable();
            $table->text('weekly_chart_notes')->nullable();
            $table->string('daily_chart_screenshot_path')->nullable();
            $table->text('daily_chart_notes')->nullable();
            $table->string('plan_a_screenshot_path')->nullable();
            $table->text('plan_a_notes')->nullable();
            $table->string('plan_b_screenshot_path')->nullable();
            $table->text('plan_b_notes')->nullable();
            $table->string('cancel_condition')->nullable();
            $table->text('notes_review')->nullable();
            $table->text('weekly_review_q1')->nullable();
            $table->text('weekly_review_q2')->nullable();
            $table->text('weekly_review_q3')->nullable();
            $table->text('weekly_review_q4')->nullable();
            $table->text('weekly_review_q5')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_plans');
    }
};
