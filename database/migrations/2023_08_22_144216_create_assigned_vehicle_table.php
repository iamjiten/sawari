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
        Schema::create('assigned_vehicle', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('order_id')->constrained('rental_orders');
            $table->primary(['vehicle_id', 'order_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigned_vehicle');
    }
};
