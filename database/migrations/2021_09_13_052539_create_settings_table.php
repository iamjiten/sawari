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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->comment('Brand , Model, Color, Reason'); // should be enum
            $table->string('value');
            $table->json('value_json')->nullable();
            $table->integer('parent_id')->nullable();
            $table->boolean('editable')->default(0);
            $table->integer('display_order')->default(0);
            $table->boolean('status')->default(1)->comment('1 means active and 0 means inactive'); // should be enum
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
        Schema::dropIfExists('settings');
    }
};
