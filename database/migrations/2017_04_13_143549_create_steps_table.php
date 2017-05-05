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
            $table->integer('responsible')->unsigned();
            $table->string('morphs_from');
            $table->text('description');
            $table->integer('ordination');
            $table->integer('previous_step')->unsigned()->nullable();

            $table->tinyInteger('status')->default(1);

            $table->foreign('previous_step')
                    ->references('id')->on('steps')
                    ->onDelete('set null');

            $table->foreign('responsible')
                    ->references('id')->on('user_types')
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('steps');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
