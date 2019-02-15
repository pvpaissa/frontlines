<?php

namespace Cleanse\Pvpaissa\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddFrontlinesTables extends Migration
{
    public function up()
    {
        Schema::create('cleanse_frontlines_overall', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('player_id')->unsigned()->index();
            $table->integer('rank')->default(1);
            $table->integer('wins')->default(0)->index();
            $table->string('percent')->default('0');
            $table->integer('matches')->default(1);
            $table->timestamps();
        });

        Schema::create('cleanse_frontlines_week', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('player_id')->unsigned()->index();
            $table->integer('rank')->default(1);
            $table->integer('wins')->default(0)->index();
            $table->string('percent')->default('0');
            $table->integer('matches')->default(1);
            $table->string('week')->default('0');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('cleanse_frontlines_overall');
        Schema::drop('cleanse_frontlines_week');
    }
}