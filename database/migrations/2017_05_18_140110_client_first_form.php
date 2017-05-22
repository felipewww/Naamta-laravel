<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientFirstForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_first_forms', function (Blueprint $table) {
            $table->integer('client_id')->unsigned();
            $table->enum('status',[0,1]);
            $table->text('first_field');
            $table->text('second_field');
            $table->text('third_field');
            $table->timestamps();

            $table->foreign('client_id')
                ->references('id')->on('clients')
                ->onDelete('cascade');
        });

        DB::statement("ALTER TABLE client_first_forms ADD form_json MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('client_first_forms');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
