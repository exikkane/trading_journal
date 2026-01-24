<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trading_systems', function (Blueprint $table) {
            $table->id();
            $table->string('hero_image_path')->nullable();
            $table->string('brand')->nullable();
            $table->string('title_primary')->nullable();
            $table->string('title_secondary')->nullable();
            $table->text('body_line_1')->nullable();
            $table->text('body_line_2')->nullable();
            $table->text('body_line_3')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->text('footer_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_systems');
    }
};
