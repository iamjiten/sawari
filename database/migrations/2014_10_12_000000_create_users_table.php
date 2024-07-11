<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gender');
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->unique();
            $table->string('photo')->nullable();
            $table->string('password')->nullable();
            $table->string('dob');
            $table->boolean('status')->default(1)->comment('0 inactive && 1 active');
            $table->boolean('is_online')->default(0)->comment('0 offline && 1 online');
            $table->boolean('is_changed')->default(0)->comment('0 password not changed && 1 password changed');
            $table->timestamp('last_seen')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('type')->default(1)->comment('1 customer && 2 rider && 3 admin');
            $table->string('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->integer('kyc_status')->nullable()->comment('1 pending, 2 approved && 3 rejected | 4 reviewing');
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('users');
    }
};

