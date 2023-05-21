<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id');
            $table->string('user_name')->nullable(true);
            $table->string('email')->unique();

            $table->string('password');
            $table->string('visible_password');
            $table->rememberToken();
            $table->string('mobile1',15)->nullable(true);

            $table->foreignId('user_type_id')->references('id')->on('user_types')->onDelete('cascade');
            $table->foreignId('pay_out_slab_id')->references('id')->on('pay_out_slabs')->onDelete('cascade');
            $table->decimal('commission')->default(0);
            $table->decimal('payout')->default(0);
            $table->decimal('opening_balance',50,2)->default(0);
            $table->decimal('closing_balance',50,2)->default(0);
            $table->integer('auto_claim')->default(0);
            $table->String('mac_address')->nullable(true);
            $table->String('temp_mac_address')->nullable(true);
            $table->integer('login_activate')->default(0);
            $table->string('platform')->nullable(true);
            $table->string('version')->nullable(true);
//            $table->integer('auto_claim')->default(0);
            $table ->integer('created_by')->nullable(true);
            $table->tinyInteger('blocked')->default(0);
            $table->tinyInteger('inforce')->default(1);
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
        Schema::dropIfExists('users');
    }
}
