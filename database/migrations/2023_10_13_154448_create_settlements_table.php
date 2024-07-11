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
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('dr | cr');
            $table->integer('channel')->comment('1 cod | 2 esewa | 3 khalti');
            $table->foreignId('trip_id')->nullable()->constrained('trips');
            $table->foreignId('user_id')->constrained('users');
            $table->double('actual_amount', 9, 2);
            $table->double('settlement_amount', 9, 2);
            $table->double('settlement_percentage', 5, 2)->nullable();
            $table->double('earned_amount', 9, 2)->nullable();
            $table->double('total_earned_amount', 9, 2);
            $table->double('total_settlement_amount', 9, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
