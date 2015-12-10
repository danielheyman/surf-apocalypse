<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemTypesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('item_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->smallInteger('icon')->unsigned()->nullable();
            $table->smallInteger('sprite')->unsigned()->nullable();
            $table->smallInteger('character_type')->unsigned();
            $table->decimal('find_chance', 5, 2);
            $table->smallInteger('find_min')->unsigned();
            $table->smallInteger('find_max')->unsigned();
            $table->smallInteger('item_type')->unsigned();
            $table->json('upgradable')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('item_types');
    }
}
