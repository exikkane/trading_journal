<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trading_plans', function (Blueprint $table) {
            $table->string('left_image_1_path')->nullable()->after('narrative');
            $table->string('left_image_2_path')->nullable()->after('left_image_1_path');
            $table->string('left_image_3_path')->nullable()->after('left_image_2_path');
        });
    }

    public function down(): void
    {
        Schema::table('trading_plans', function (Blueprint $table) {
            $table->dropColumn(['left_image_1_path', 'left_image_2_path', 'left_image_3_path']);
        });
    }
};
