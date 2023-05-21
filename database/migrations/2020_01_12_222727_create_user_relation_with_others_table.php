<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRelationWithOthersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_relation_with_others', function (Blueprint $table) {
            $table->id();
//            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
//            $table->foreignId('parent_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('super_stockist_id');
            $table->integer('stockist_id')->nullable(true);
            $table->integer('terminal_id')->nullable(true);
            $table->integer('changed_by')->nullable(true);
            $table->integer('changed_for')->nullable(true);
            $table->date('end_date')->nullable(true);
            $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('user_relation_with_others');
    }
}
