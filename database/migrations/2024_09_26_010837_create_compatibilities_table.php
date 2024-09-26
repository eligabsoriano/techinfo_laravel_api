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
        Schema::create('compatibilities', function (Blueprint $table) {
            $table->id('compatibility_id');
            $table->foreignId('processor_id')->constrained('processors', 'processor_id')->onDelete('cascade');
            $table->foreignId('motherboard_id')->constrained('motherboards', 'motherboard_id')->onDelete('cascade');
            $table->foreignId('ram_id')->constrained('rams', 'ram_id')->onDelete('cascade');
            $table->foreignId('gpu_id')->constrained('gpus', 'gpu_id')->onDelete('cascade');
            $table->foreignId('psu_id')->constrained('power_supply_units', 'psu_id')->onDelete('cascade');
            $table->foreignId('case_id')->constrained('computer_cases', 'case_id')->onDelete('cascade');
            $table->foreignId('cooler_id')->constrained('cpu_coolers', 'cooler_id')->onDelete('cascade');
            $table->foreignId('hdd_id')->nullable()->constrained('hdds', 'hdd_id')->onDelete('cascade');
            $table->foreignId('ssd_id')->nullable()->constrained('ssds', 'ssd_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compatibilities');
    }
};
