<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->morphs('order');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); //rider's user id
            $table->float('amount', 9, 2);
            $table->integer('status')->default(0)->comment('0 Assigned | 1 Completed | 2 Cancelled');
            $table->foreignId('reason_id')->nullable()->constrained('settings')->cascadeOnDelete();
            $table->foreignId('action_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('action_at')->default(now());
            $table->json('extra')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trips');
    }
};
