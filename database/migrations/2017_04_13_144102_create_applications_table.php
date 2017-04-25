<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned()->nullable();
            $table->integer('staff_id')->unsigned()->nullable();
            $table->text('description');

            $table->foreign('client_id')
                    ->references('id')->on('clients')
                    ->onDelete('set null');

            $table->foreign('staff_id')
                    ->references('id')->on('users')
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
        Schema::dropIfExists('applications');
    }
}
