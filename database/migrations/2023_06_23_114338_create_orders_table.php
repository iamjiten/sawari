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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->index();
            $table->integer('token')->index()->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('delivery_type_id')->constrained('delivery_types')->cascadeOnDelete();
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->integer('status')->default(0)->comment('0 Pending | 1 Received | 2 Assigned | 3 On Pickup Location | 4 On Way | 5 On Drop Location | 6 Delivered | 7 Cancelled');
            $table->float('actual_amount', 9, 2)->default(0);
            $table->float('discount_amount', 9, 2)->default(0);
            $table->float('net_amount', 9, 2)->default(0);
            $table->string('promo_code')->nullable();
            $table->json('extra')->nullable()->comment('Base fare , Distance Cost, Delivery Cost, Vat, Package Cost');
            $table->json('route')->comment('array of long and lat of packages receiver of order');
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
        Schema::dropIfExists('orders');
    }
};
