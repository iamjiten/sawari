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
        Schema::create('citizenships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('citizenship_number')->index();
            $table->string('front_image')->nullable();
            $table->string('back_image')->nullable();
            $table->string('confirmation_image')->nullable();
            $table->date('issued_at');
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
        Schema::dropIfExists('citizenships');
    }
};
