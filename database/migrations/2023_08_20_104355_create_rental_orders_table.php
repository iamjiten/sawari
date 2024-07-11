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
        Schema::create('rental_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->integer('status')->default(0)->comment('0 pending | 1 received | 2 assigned | 3 completed | 4 cancelled');
            $table->double('actual_amount', 9, 2);
            $table->double('discount_amount', 9, 2)->default(0);
            $table->double('net_amount', 9, 2);
            $table->foreignId('pickup_location_id')->constrained('rental_locations');
            $table->foreignId('drop_off_location_id')->constrained('rental_locations');
            $table->dateTime('pickup_date');
            $table->dateTime('drop_off_date');
            $table->integer('withDriver');
            $table->json('extra')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_orders');
    }
};
