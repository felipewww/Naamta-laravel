<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('steps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description');
            $table->integer('previous_step')->unsigned()->nullable();
            $table->integer('form')->unsigned()->nullable();
            $table->integer('screen')->unsigned()->nullable();
//            $table->integer('responsible')->unsigned();
//            $table->tinyInteger('status')->default(0);

            $table->foreign('previous_step')
                    ->references('id')->on('steps')
                    ->onDelete('set null');

            $table->foreign('form')
                    ->references('id')->on('form_templates')
                    ->onDelete('set null');
                    
            $table->foreign('screen')
                    ->references('id')->on('screens')
                    ->onDelete('set null');

//            $table->foreign('responsible')
//                    ->references('id')->on('users')
//                    ->onDelete('cascade');

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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('steps');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
