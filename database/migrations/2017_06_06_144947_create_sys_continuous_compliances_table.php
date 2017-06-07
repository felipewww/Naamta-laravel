<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysContinuousCompliancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_continuous_compliance', function (Blueprint $table){
            $table->increments('id');
            $table->integer('application_id')->unsigned();
            $table->integer('form_template_id')->unsigned();
            $table->integer('interval');
            $table->integer('times');
            $table->integer('times_executed')->default('0');

            $table->foreign('application_id')
                ->references('id')->on('applications')
                ->onDelete('cascade');

            $table->foreign('form_template_id')
                ->references('id')->on('form_templates');

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
        Schema::dropIfExists('sys_continuous_compliance');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
