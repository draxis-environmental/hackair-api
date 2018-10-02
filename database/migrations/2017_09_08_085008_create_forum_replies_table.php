<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('forum_thread_id')->unsigned();
            $table->integer('author_id')->unsigned();
            $table->text('body');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('forum_thread_id')
                ->references('id')
                ->on('forum_threads')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('author_id')
                ->references('id')
                ->on('users')
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
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->dropForeign(['forum_thread_id']);
            $table->dropForeign(['author_id']);
        });
        Schema::drop('forum_replies');
    }
}
