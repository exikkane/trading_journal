<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('period_type', 20);
            $table->unsignedInteger('year');
            $table->unsignedTinyInteger('quarter')->nullable();
            $table->unsignedTinyInteger('month')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('mpa_metric')->nullable();
            $table->text('mpa_metric_reason')->nullable();
            $table->text('trades_conclusions')->nullable();
            $table->text('trades_errors')->nullable();
            $table->text('notes')->nullable();
            $table->json('notes_screenshots')->nullable();
            $table->text('summary_general')->nullable();
            $table->text('summary_what_works')->nullable();
            $table->text('summary_what_not')->nullable();
            $table->text('summary_key_lessons')->nullable();
            $table->text('summary_next_steps')->nullable();
            $table->timestamps();

            $table->unique(['period_type', 'year', 'quarter', 'month'], 'performance_reviews_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};
