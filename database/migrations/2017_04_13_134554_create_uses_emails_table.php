<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsesEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uses_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_id')->unsigned()->nullable();
            $table->integer('recieved_by')->unsigned()->nullable();
            
            $table->foreign('recieved_by')
                    ->references('id')->on('user_types')
                    ->onDelete('set null');

            $table->foreign('email_id')
                    ->references('id')->on('email_templates')
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
        Schema::dropIfExists('uses_emails');
    }
}
