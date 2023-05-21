<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_name',100);
            $table->bigInteger('last_counter')->default(1);
            $table->integer('accounting_year');
            $table->unique(['voucher_name','accounting_year']);
            $table->string('prefix')->nullable(true);
            $table->string('suffix')->nullable(true);
            $table->string('delimiter')->default('-');
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
        Schema::dropIfExists('custom_vouchers');
    }
}
