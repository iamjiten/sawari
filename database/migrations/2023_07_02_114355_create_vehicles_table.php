<?php

use App\Models\Package;
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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained('settings')->cascadeOnDelete();
            $table->foreignId('model_id')->constrained('settings')->cascadeOnDelete();
            $table->foreignId('color_id')->constrained('settings')->cascadeOnDelete();
            $table->string('number_plate')->index();
            $table->string('production_year');
            $table->string('image')->nullable();
            $table->string('blue_book_first_image')->nullable()->comment('insert 2nd and 3rd page photo of blue book');
            $table->string('blue_book_second_image')->nullable()->comment('insert 9th page photo of blue book');
            $table->integer('status')->default(1)->comment('1 pending | 2 approved | 3 rejected | 4 reviewing');
            $table->string('remarks')->nullable();
            $table->json('extra')->nullable();
            $table->foreignId('created_by');
            $table->foreignId('updated_by');
            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
};
