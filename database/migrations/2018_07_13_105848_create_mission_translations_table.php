<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mission_id')->unsigned();
            $table->string('name');
            $table->string('description');
            $table->string('description_short');
            $table->string('motivation');
            $table->string('cta_text');
            $table->string('feedback_success');
            $table->string('locale')->index();

            $table->unique(['mission_id','locale']);
            $table->foreign('mission_id')->references('id')->on('missions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'description_short', 'motivation', 'cta_text', 'feedback_success']);
        });
    }
}
