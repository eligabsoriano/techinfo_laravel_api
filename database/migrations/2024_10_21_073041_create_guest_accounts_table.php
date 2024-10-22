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
        Schema::create('guest_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Guest account name
            $table->string('cpu', 255)->nullable(); // Adjust string length as necessary
            $table->string('gpu', 255)->nullable();
            $table->string('ram', 255)->nullable();
            $table->string('ssd', 255)->nullable();
            $table->string('hdd', 255)->nullable();
            $table->string('cpu_cooler', 255)->nullable();
            $table->string('case', 255)->nullable();
            $table->string('psu', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_accounts');
    }
};
