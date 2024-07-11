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
        Schema::create('rental_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('rental_areas');
            $table->string('name');
            $table->string('longitude');
            $table->string('latitude');
            $table->foreignId('created_by')->constrained('users');
            $table->integer('status')->default(1)->comment('1 active | 2 inactive');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_locations');
    }
};
