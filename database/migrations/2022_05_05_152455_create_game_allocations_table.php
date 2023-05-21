<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_allocations', function (Blueprint $table) {
            $table->id();
            $table ->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('game1')->default(0);
            $table->tinyInteger('game2')->default(0);
            $table->tinyInteger('game3')->default(0);
            $table->tinyInteger('game4')->default(0);
            $table->tinyInteger('game5')->default(0);
            $table->tinyInteger('game6')->default(0);
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
        Schema::dropIfExists('game_allocations');
    }
}
