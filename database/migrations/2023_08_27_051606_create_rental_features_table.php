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
        Schema::create('rental_features', function (Blueprint $table) {
            $table->id();
            $table->string('module')->comment('rental | delivery | movers | ...');
            $table->string('category')->comment('services | basic_info | ...');
            $table->string('key')->comment('insurance | door | passenger | gear | ...');
            $table->string('value');
            $table->boolean('status')->default(1);
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_features');
    }
};
