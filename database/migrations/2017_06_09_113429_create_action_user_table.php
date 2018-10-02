<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('action_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->integer('mission_id')->unsigned()->index()->nullable();
            $table->integer('achievement_id')->unsigned()->index()->nullable();
            $table->string('points');
            $table->boolean('levelup')->default(false);
            $table->timestamps();

            $table->foreign('action_id')->references('id')->on('actions');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('mission_id')->references('id')->on('missions');
            $table->foreign('achievement_id')->references('id')->on('achievements');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
