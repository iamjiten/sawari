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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('transactional');
            $table->string('pid')->index();
            $table->foreignId('user_id')->index()->constrained('users')->cascadeOnDelete();
            $table->float('amount', 9, 2)->default(0);
            $table->integer('status')->default(0)->comment('0 Pending | 1 Ambiguous | 2 Failed | 3 Refunded | 4 Completed');
            $table->integer('channel')->default(0)->comment('0 Manual | 1 Esewa | 2 Khalti | 3 Connect IPS | 4 Fone Pay');
            $table->integer('parent_id')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
