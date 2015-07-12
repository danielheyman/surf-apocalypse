<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHouseItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('house_items', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('loc_x')->unsigned();
            $table->smallInteger('loc_y')->unsigned();

            $table->integer('house_id')->unsigned();
            $table->foreign('house_id')->references('id')->on('houses')->onDelete('cascade');
            $table->integer('item_type_id')->unsigned();
            $table->foreign('item_type_id')->references('id')->on('item_types')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('house_items');
    }
}
