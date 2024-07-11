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
        Schema::create('vehicle_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->json('detail_info');
            $table->double('per_day_fare', 9, 2);
            $table->double('per_day_driver_fare', 9, 2)->nullable();
            $table->integer('withDriver')->default(2)->comment('0 no driver| 1 with driver | 2 might');
            $table->integer('discount_percent')->default(0);
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_informations');
    }
};
