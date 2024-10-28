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
        Schema::create('computer_cases', function (Blueprint $table) {
            $table->id('case_id');
            $table->string('case_name');
            $table->string('brand');
            $table->string('form_factor_supported');
            $table->string('max_gpu_length_mm');
            $table->string('max_hdd_count');
            $table->string('max_ssd_count');
            $table->string('current_hdd_count');
            $table->string('current_ssd_count');
            $table->string('max_cooler_height_mm');
            $table->string('link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computer_cases');
    }
};
