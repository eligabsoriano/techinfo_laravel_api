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
            $table->string('interface_type');
            $table->integer('tdp_wattage');
            $table->integer('gpu_length_mm');
            $table->text('link')->nullable();
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
