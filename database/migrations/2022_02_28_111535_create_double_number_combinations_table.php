<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoubleNumberCombinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('double_number_combinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('single_number_id')->references('id')->on('single_numbers')->onDelete('cascade');
            $table->integer('double_number')->nullable(false)->unique();
            $table->string('visible_double_number',3)->nullable(false);
            $table->foreignId('andar_number_id')->references('id')->on('andar_numbers')->onDelete('cascade');
            $table->foreignId('bahar_number_id')->references('id')->on('bahar_numbers')->onDelete('cascade');
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
        Schema::dropIfExists('double_number_combinations');
    }
}
