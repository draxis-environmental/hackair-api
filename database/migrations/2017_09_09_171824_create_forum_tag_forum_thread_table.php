<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTagForumThreadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_tag_forum_thread', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('forum_thread_id')->unsigned();
            $table->integer('forum_tag_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('forum_thread_id')
                ->references('id')
                ->on('forum_threads')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('forum_tag_id')
                ->references('id')
                ->on('forum_tags')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('forum_tag_forum_thread');
    }
}
