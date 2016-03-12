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
            $table->integer('sprite')->unsigned()->nullable();
            $table->smallInteger('users_allowed')->unsigned();
            $table->decimal('find_chance', 5, 2)->default(0);
            $table->boolean('find_decimal')->default(false);
            $table->smallInteger('find_min')->unsigned()->default(0);
            $table->smallInteger('find_max')->unsigned()->default(0);
            $table->smallInteger('item_type')->unsigned();
            $table->json('attributes')->nullable();
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
