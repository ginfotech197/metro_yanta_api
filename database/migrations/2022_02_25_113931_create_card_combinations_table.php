<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardCombinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_combinations', function (Blueprint $table) {
            $table->id();
            $table->string('rank_name')->nullable(false);
            $table->string('suit_name')->nullable(false);
            $table->string('rank_initial')->nullable(false);
            $table ->foreignId('card_combination_type_id')->references('id')->on('card_combination_types')->onDelete('cascade');
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
        Schema::dropIfExists('card_combinations');
    }
}
