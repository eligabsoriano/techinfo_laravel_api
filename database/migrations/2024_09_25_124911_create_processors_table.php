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
            $table->integer('tdp_wattage');
            $table->decimal('base_clock_speed', 4, 2);
            $table->decimal('max_clock_speed', 4, 2);
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