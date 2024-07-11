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
        Schema::create('rental_location_vehicle', function (Blueprint $table) {
            $table->foreignId('rental_location_id')->constrained('rental_locations');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->primary(['rental_location_id', 'vehicle_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_location_vehicle');
    }
};
