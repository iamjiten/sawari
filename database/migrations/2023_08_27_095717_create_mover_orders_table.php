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
        Schema::create('mover_orders', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->index();
            $table->foreignId('user_id')->constrained('users');
            $table->integer('status')->default(0);
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types');
            $table->decimal('actual_amount', 9, 2);
            $table->decimal('discount_amount', 9,2);
            $table->decimal('net_amount', 9,2);
            $table->string('shifting_from_address');
            $table->string('shifting_from_longitude');
            $table->string('shifting_from_latitude');
            $table->string('shifting_to_address');
            $table->string('shifting_to_longitude');
            $table->string('shifting_to_latitude');
            $table->dateTime('shifting_at');
            $table->integer('no_of_rooms')->default(1);
            $table->decimal('galli_distance',9,2)->nullable();
            $table->integer('no_of_loader')->nullable();
            $table->integer('no_of_trips')->default(1);
            $table->json('extra');
            $table->json('route');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mover_orders');
    }
};
