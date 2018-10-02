<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSocialActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_social_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('social_activity_id');
            $table->timestamps();
            $table->integer('counter')->default(1);
            $table->tinyInteger('visible')->default(1);
            $table->string('object_metadata')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('social_activity_id')->references('id')->on('social_activities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_social_activity');
    }
}
