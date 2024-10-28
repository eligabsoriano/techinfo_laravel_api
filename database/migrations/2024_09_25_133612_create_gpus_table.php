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
        Schema::create('gpuses', function (Blueprint $table) {
            $table->id('gpu_id');
            $table->string('gpu_name');
            $table->string('brand');
            $table->string('cuda_cores')->nullable();
            $table->string('compute_units')->nullable();
            $table->string('stream_processors')->nullable();
            $table->string('game_clock_ghz')->nullable();
            $table->string('base_clock_ghz');
            $table->string('boost_clock_ghz');
            $table->string('memory_size_gb');
            $table->string('memory_type');
            $table->string('memory_interface_bits');
            $table->string('tdp_wattage');
            $table->string('gpu_length_mm');
            $table->string('required_power');
            $table->string('required_6_pin_connectors');
            $table->string('required_8_pin_connectors');
            $table->string('required_12_pin_connectors')->nullable();
            $table->string('link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gpuses');
    }
};
