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
            $table->text('text');
            $table->integer('field')->unsigned()->nullable();
            $table->integer('response')->unsigned()->nullable();
            $table->enum('status', array("wait", "viewed", "resolved"))->default("wait");

            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('set null');

            $table->foreign('field')
                    ->references('id')->on('fields')
                    ->onDelete('set null');

            $table->foreign('response')
                    ->references('id')->on('comments')
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('comments');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
