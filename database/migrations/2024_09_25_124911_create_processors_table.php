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
        Schema::create('processors', function (Blueprint $table) {
            $table->id('processor_id');
            $table->string('processor_name');
            $table->string('brand');
            $table->string('socket_type');
            $table->string('compatible_chipsets')->nullable();
            $table->integer('cores');
            $table->integer('threads');
            $table->string('base_clock_speed');  // in GHz
            $table->string('max_turbo_boost_clock_speed');  // in GHz
            $table->string('tdp');
            $table->integer('cache_size_mb');  // Cache in MB
            $table->string('integrated_graphics')->nullable();
            $table->string('link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processors');
    }
};
