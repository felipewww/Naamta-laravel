<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned()->nullable();
            $table->integer('field')->unsigned()->nullable();
            $table->text('value');
            
            $table->foreign('client_id')
                    ->references('id')->on('clients')
                    ->onDelete('set null');

            $table->foreign('field')
                    ->references('id')->on('fields')
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
        Schema::dropIfExists('form_values');
    }
}
