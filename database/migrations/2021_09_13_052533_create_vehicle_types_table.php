<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('weight_capacity');
            $table->string('weight_unit')->default('kg')->comment('kg, gram, liter');
            $table->string('icon')->nullable();
            $table->string('distance_unit')->default('km')->comment('km, m, cm');
            $table->float('per_distance_unit_cost', 9, 2);
            $table->float('base_fare', 9, 2);
            $table->boolean('status')->default(1)->comment('1 means active and 0 means inactive');
            $table->json('extra')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_types');
    }
};
