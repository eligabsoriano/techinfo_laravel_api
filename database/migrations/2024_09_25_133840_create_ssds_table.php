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
        Schema::create('ssds', function (Blueprint $table) {
            $table->id('ssd_id');
            $table->string('ssd_name');
            $table->string('brand');
            $table->string('interface_type');
            $table->integer('capacity_gb');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ssds');
    }
};
