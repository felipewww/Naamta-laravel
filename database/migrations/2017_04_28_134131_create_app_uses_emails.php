<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppUsesEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_uses_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_id')->unsigned();
            $table->integer('received_by')->unsigned();
            $table->integer('application_step_id')->unsigned();

            $table->enum('send_when', ['success', 'rejected']);

            $table->foreign('received_by')
                ->references('id')->on('application_user_types')
                ->onDelete('cascade');

            $table->foreign('email_id')
                ->references('id')->on('email_templates')
                ->onDelete('cascade');

            $table->foreign('application_step_id')
                ->references('id')->on('application_steps')
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
        Schema::dropIfExists('application_uses_emails');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
