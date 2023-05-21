<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechargeToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharge_to_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_uid')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('recharge_done_by_uid')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('old_amount',50,2)->default(0);
            $table->decimal('amount',50,2)->default(0);
            $table->decimal('new_amount',50,2)->default(0);
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
        Schema::dropIfExists('recharge_to_users');
    }
}
