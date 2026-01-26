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
        Schema::create('trading_plan_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_plan_id')->constrained()->cascadeOnDelete();
            $table->date('update_date');
            $table->text('update_notes')->nullable();
            $table->json('update_screenshots')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trading_plan_updates');
    }
};
