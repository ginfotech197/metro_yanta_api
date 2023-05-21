<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terminal_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('play_master_id')->default(0);
            $table->string('description')->nullable(true);
            $table->decimal('old_amount',50,2)->default(0);
            $table->decimal('recharged_amount',50,2)->default(0);
            $table->decimal('played_amount',50,2)->default(0);
            $table->decimal('prize_amount',50,2)->default(0);
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
        Schema::dropIfExists('transactions');
    }
}
