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
        Schema::create('vehicle_order', function (Blueprint $table) {
            $table->foreignId('order_id')->constrained('rental_orders');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->timestamps();
            $table->softDeletes();
            $table->primary(['order_id', 'vehicle_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_order');
    }
};
