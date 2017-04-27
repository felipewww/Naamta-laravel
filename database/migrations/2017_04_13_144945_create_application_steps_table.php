<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('step_id')->unsigned()->nullable();
            $table->integer('responsible')->unsigned();
            $table->integer('form')->unsigned()->nullable();
            $table->integer('screen')->unsigned()->nullable();
            $table->tinyInteger('status')->default(0);

            $table->foreign('step_id')
                    ->references('id')->on('steps')
                    ->onDelete('set null');

            $table->foreign('form')
                    ->references('id')->on('steps')
                    ->onDelete('set null');

            $table->foreign('screen')
                    ->references('id')->on('steps')
                    ->onDelete('set null');

            $table->foreign('responsible')
                    ->references('id')->on('users')
                    ->onDelete('cascade');

            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_steps');
    }
}
