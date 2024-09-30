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
