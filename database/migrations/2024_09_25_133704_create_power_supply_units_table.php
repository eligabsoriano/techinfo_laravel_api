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
            $table->integer('wattage');
            $table->string('efficiency_rating');
            $table->boolean('has_required_connectors');
            $table->text('link')->nullable();
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
