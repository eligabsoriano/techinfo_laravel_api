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
        Schema::create('motherboards', function (Blueprint $table) {
            $table->id('motherboard_id');
            $table->string('motherboard_name');
            $table->string('brand');
            $table->string('socket_type');
            $table->string('ram_type');
            $table->integer('max_ram_slots');
            $table->string('max_ram_capacity'); // Add max_ram_capacity
            $table->string('max_ram_speed');    // Add max_ram_speed
            $table->string('supported_ram_type'); // Add supported_ram_type
            $table->string('chipset'); // Add chipset
            $table->boolean('has_pcie_slot');    // Add has_pcie_slot
            $table->boolean('has_sata_ports');   // Add has_sata_ports
            $table->boolean('has_m2_slot');      // Add has_m2_slot
            $table->string('gpu_interface');
            $table->string('form_factor');
            $table->text('link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motherboards');
    }
};
