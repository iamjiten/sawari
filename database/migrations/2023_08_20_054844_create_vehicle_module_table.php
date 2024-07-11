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
        Schema::create('vehicle_module', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('module_id')->constrained('modules');
            $table->primary(['vehicle_id', 'module_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_module');
    }
};
