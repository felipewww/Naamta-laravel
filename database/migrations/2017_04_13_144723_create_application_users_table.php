<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_users', function (Blueprint $table) {
            $table->increments('id');
//            $table->integer('application_id')->unsigned()->nullable();
//            $table->integer('user_id')->unsigned()->nullable();
//            $table->integer('user_type')->unsigned()->nullable();
            $table->integer('application_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('user_type')->unsigned();

            $table->foreign('application_id')
                    ->references('id')->on('applications')
                    ->onDelete('cascade');

            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');

            $table->foreign('user_type')
                    ->references('id')->on('application_user_types')
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
        Schema::dropIfExists('application_users');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
