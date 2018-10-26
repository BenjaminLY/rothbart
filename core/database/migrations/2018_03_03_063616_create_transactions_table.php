<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference')->index();
            $table->integer('user_id')->unsigned()->index();
            $table->integer('plan_id')->unsigned()->index();
            $table->integer('movie_id')->unsigned()->index();
            $table->string('transaction_id')->index();
            $table->enum('status', ['pending', 'paid'])->index();
            $table->text('logs')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('plan_id')
                ->references('id')
                ->on('plans');
            $table->foreign('movie_id')
                ->references('id')
                ->on('movies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
