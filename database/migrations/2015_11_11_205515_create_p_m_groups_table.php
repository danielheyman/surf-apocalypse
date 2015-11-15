<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePMGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pm_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->timestamp('user_last_seen');
            $table->timestamp('user2_last_seen');
            $table->timestamp('user_last_message');
            $table->timestamp('user2_last_message');


            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('user2_id')->unsigned();
            $table->foreign('user2_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('p_m_groups');
    }
}
