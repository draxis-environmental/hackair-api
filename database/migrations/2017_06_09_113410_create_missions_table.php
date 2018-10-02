<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable(); // Defines how the row will be validated
            $table->string('name');
            $table->text('description');
            $table->string('description_short')->nullable();
            $table->text('motivation');
            $table->string('display_picture')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->text('feedback_success');
            $table->integer('action_id')->unsigned()->index()->nullable();
            $table->integer('points')->unsigned()->default(0);
            $table->integer('achievement_id')->unsigned()->index()->nullable();
            $table->dateTime('datetime_starts')->nullable();
            $table->dateTime('datetime_ends')->nullable();
            $table->boolean('visible_web')->default(false);
            $table->boolean('visible_mobile')->default(false);
            $table->timestamps();
            
            $table->foreign('action_id')->references('id')->on('actions');
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
