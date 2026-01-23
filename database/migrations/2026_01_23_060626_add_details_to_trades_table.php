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
            $table->string('execution')->nullable();
            $table->string('entry_tf')->nullable();
            $table->text('idea_notes')->nullable();
            $table->text('conclusions_notes')->nullable();
            $table->string('idea_screenshot_path')->nullable();
            $table->string('exit_screenshot_path')->nullable();
            $table->string('conclusion_screenshot_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn([
                'execution',
                'entry_tf',
                'idea_notes',
                'conclusions_notes',
                'idea_screenshot_path',
                'exit_screenshot_path',
                'conclusion_screenshot_path',
            ]);
        });
    }
};
