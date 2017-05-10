<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->text('user_name');
            $table->integer('field')->unsigned()->nullable();
            $table->text('text');

            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('set null');

            $table->foreign('field')
                    ->references('id')->on('fields')
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
        Schema::dropIfExists('comments');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
