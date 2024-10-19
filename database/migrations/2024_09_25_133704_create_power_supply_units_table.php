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
        Schema::create('power_supply_units', function (Blueprint $table) {
            $table->id('psu_id');
            $table->string('psu_name');
            $table->string('brand');
            $table->string('wattage');
            $table->string('continuous_wattage');
            $table->integer('gpu_6_pin_connectors');
            $table->integer('gpu_8_pin_connectors');
            $table->integer('gpu_12_pin_connectors');
            $table->string('efficiency_rating');
            $table->boolean('has_required_connectors');
            $table->string('link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_supply_units');
    }
};
