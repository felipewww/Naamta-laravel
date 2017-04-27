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
            $table->integer('previous_step')->unsigned()->nullable();
            $table->integer('responsible')->unsigned()->nullable();
            $table->integer('form')->unsigned()->nullable();
            $table->integer('screen')->unsigned()->nullable();
            $table->tinyInteger('status')->default(0);

            $table->foreign('responsible')
                    ->references('id')->on('users')
                    ->onDelete('set null');

            $table->foreign('previous_step')
                    ->references('id')->on('application_steps')
                    ->onDelete('set null');

            $table->foreign('form')
                    ->references('id')->on('form_templates')
                    ->onDelete('set null');
                    
            $table->foreign('screen')
                    ->references('id')->on('screens')
                    ->onDelete('set null');

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
