<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAchievementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable(); // Defines how the row will be validated
            $table->string('name')->unique();
            $table->text('description');
            $table->text('motivation');
            $table->string('display_picture')->nullable();
            $table->integer('action_id')->unsigned()->index()->nullable();
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
