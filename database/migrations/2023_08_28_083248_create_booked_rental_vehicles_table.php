<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booked_rental_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('rental_orders');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->dateTime('from');
            $table->dateTime('to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booked_rental_vehicles');
    }
};
