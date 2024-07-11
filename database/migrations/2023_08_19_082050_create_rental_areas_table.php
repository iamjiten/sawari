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
        Schema::create('rental_areas', function (Blueprint $table) {
            $table->id();
            $table->integer('province');
            $table->string('district');
            $table->string('city');
            $table->string('area');
            $table->integer('status')->default(1)->comment('active | inactive');
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_areas');
    }
};
