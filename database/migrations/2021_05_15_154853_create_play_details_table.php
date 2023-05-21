<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('play_details', function (Blueprint $table) {
            $table->id();
            $table ->foreignId('play_master_id')->references('id')->on('play_masters')->onDelete('cascade');
            $table ->foreignId('game_type_id')->references('id')->on('game_types')->onDelete('cascade');
            $table ->integer('combination_number_id');
            $table->integer('quantity')->nullable(false);
            $table->integer('series_id')->default(0);
            $table->decimal('mrp',5,4)->default(0);
            $table->decimal('commission',10,2)->default(0);
            $table->decimal('ps_commission',10,2)->default(0);
            $table->decimal('stockist_commission',10,2)->default(0);
            $table->decimal('pss_commission',10,2)->default(0);
            $table->decimal('super_stockist_commission',10,2)->default(0);
            $table->decimal('terminal_payout',10,2)->default(0);
            $table->tinyInteger('combined_number')->default(1);
//            $table->integer('multiplexer')->nullable(false);
            $table->decimal('global_payout',10,2)->default(0);

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
        Schema::dropIfExists('play_details');
    }
}
