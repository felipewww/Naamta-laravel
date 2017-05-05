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
//            $table->increments('id');
            $table->integer('email_id')->unsigned();
            $table->integer('received_by')->unsigned();
            $table->integer('step_id')->unsigned();

            $table->enum('send_when', ['success', 'rejected']);
            
            $table->foreign('received_by')
                    ->references('id')->on('user_types')
                    ->onDelete('cascade');

            $table->foreign('email_id')
                    ->references('id')->on('email_templates')
                    ->onDelete('cascade');

            $table->foreign('step_id')
                ->references('id')->on('steps')
                ->onDelete('cascade');

            $table->primary(['email_id', 'received_by', 'step_id'], 'primary_three_fk');

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
        Schema::dropIfExists('uses_emails');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
