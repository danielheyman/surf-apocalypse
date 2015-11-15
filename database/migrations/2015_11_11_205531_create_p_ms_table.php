<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePMsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pms', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->boolean('sender');
            $table->text('message');
            $table->string('event')->nullable();
            $table->json('event_data')->nullable();

            $table->integer('pm_group_id')->unsigned();
            $table->foreign('pm_group_id')->references('id')->on('pm_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pms');
    }
}
