<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerifiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_verifiers', function (Blueprint $table){
            $table->increments('id');

            $table->integer('position');

            $table->integer('application_id')->unsigned();

            $table->foreign('application_id')
                ->references('id')->on('applications')
                ->onDelete('cascade');

            $table->integer('user_id')->unsigned();

            $table->foreign('user_id')
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('application_verifiers');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
