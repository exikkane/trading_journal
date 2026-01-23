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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('account');
            $table->enum('direction', ['long', 'short']);
            $table->string('pair');
            $table->enum('result', ['in_progress', 'loss', 'win', 'be'])->default('in_progress');
            $table->decimal('risk_reward', 6, 2);
            $table->decimal('risk_pct', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
