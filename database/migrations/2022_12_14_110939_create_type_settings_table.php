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
        Schema::create('type_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('icon')->nullable();
            $table->double('price', 9, 2)->default(0.00);
            $table->string('type')->default('category')->comment('category & sensible');
            $table->boolean('status')->default(1)->comment('0 inactive && 1 active');
            $table->json('extra')->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('created_by');
            $table->foreignId('updated_by');
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
        Schema::dropIfExists('type_settings');
    }
};
