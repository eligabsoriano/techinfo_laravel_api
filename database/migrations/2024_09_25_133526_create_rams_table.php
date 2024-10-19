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
        Schema::create('rams', function (Blueprint $table) {
            $table->id('ram_id');
            $table->string('ram_name');
            $table->string('brand');
            $table->string('ram_type');          // e.g., DDR4, DDR5
            $table->string('ram_capacity_gb');  // Capacity in GB (e.g., 32)
            $table->string('ram_speed_mhz');    // Speed in MHz (e.g., 4000)
            $table->string('cas_latency');       // e.g., CL18
            $table->string('power_consumption'); // Power consumption in watts (   convert)
            $table->string('link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rams');
    }
};
