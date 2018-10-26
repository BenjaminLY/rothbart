<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanUserTable extends Migration
{

    public function up()
    {
        Schema::create('plan_user', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('plan_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('plan_id')
                ->references('id')
                ->on('plans');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');

        });
    }

    public function down()
    {
        Schema::drop('plan_user');
    }
}
