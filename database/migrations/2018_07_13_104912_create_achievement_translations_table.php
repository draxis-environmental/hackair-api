<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAchievementTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('achievement_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('achievement_id')->unsigned();
            $table->string('name');
            $table->text('description');
            $table->text('motivation');
            $table->string('locale')->index();

            $table->unique(['achievement_id','locale']);
            $table->foreign('achievement_id')->references('id')->on('achievements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'motivation']);
        });
    }
}
