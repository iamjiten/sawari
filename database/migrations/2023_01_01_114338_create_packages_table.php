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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name');
            $table->foreignId('sender_id');
            $table->foreignId('receiver_id');
            $table->boolean('is_receiver_user');
            $table->foreignId('package_category_id');
            $table->foreignId('package_sensible_id')->nullable();
            $table->foreignId('package_size_id')->constrained('package_sizes');
            $table->string('sender_address');
            $table->string('sender_latitude');
            $table->string('sender_longitude');
            $table->string('receiver_address');
            $table->string('receiver_latitude');
            $table->string('receiver_longitude');
//            $table->json('route')->comment('array of long and lat of packages receiver of order');
            $table->string('sender_receiver_distance_unit')->default('km')->comment('km, m, cm');
            $table->float('sender_receiver_distance');
            $table->float('amount', 9, 2)->default(0);
            $table->integer('status')->default(1)->comment('1 pending | 2 processing | 3 delivered');
            $table->json('extra')->nullable();
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
        Schema::dropIfExists('packages');
    }
};
