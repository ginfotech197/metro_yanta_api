<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('draw_master_id')->references('id')->on('draw_masters')->onDelete('cascade');
//            $table->foreignId('number_combination_id')->references('id')->on('number_combinations')->onDelete('cascade');
            $table ->integer('combination_number_id');
            $table ->foreignId('game_type_id')->references('id')->on('game_types')->onDelete('cascade');
            $table ->integer('multiplexer');

//            $table ->foreignId('game_id')->references('id')->on('games')->onDelete('cascade');


            $table->date('game_date');

            $table->tinyInteger('inforce')->default(1);
            $table->timestamps();

            $table->unique(['draw_master_id', 'game_date', 'game_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_results');
    }
}
