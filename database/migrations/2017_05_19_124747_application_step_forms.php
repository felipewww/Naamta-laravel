<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ApplicationStepForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_step_forms', function (Blueprint $table) {
            $table->integer('application_step_id')->unsigned();
            $table->integer('form_templates_id')->unsigned();
            $table->string('mform_id');

            $table->foreign('application_step_id')
                ->references('id')->on('application_steps');

            $table->foreign('form_templates_id')
                ->references('id')->on('form_templates');
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
        Schema::dropIfExists('application_step_forms');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
