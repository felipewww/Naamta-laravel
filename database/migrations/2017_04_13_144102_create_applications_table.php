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
            $table->text('description');
            $table->enum('type', ['new','cloned'])->default('new');
            $table->enum('status',
                [
                    0, //Status 4 - Registration form accepted, waiting staff set up the workflow (steps)
                    1, //Status 5 - Steps were configured, the application is able to be filled by applicant
                    'denied', //Only if Status 3 will be rejected
                    'wt_payment',  //Status 3 - with the RegForm filled, staff needs to validate info and payment, if there is something wrong, go to DENIED and wait refill
                    'wt_firstform',  //Status 2 - Right after email confirm, the applicant needs to fill the Registration Form (first form)
                    'wt_emailconfirm', //Status 1 - right after register, the applicatn needs to confirm e-mail to verify that isn't a robot
                    'completed' //When workflow is entirely filled, the application will be considered completed, allowed to receive new flows (Monthly, Quarterly, Annually and etc.)
                ])
                ->default(0);

            $table->foreign('client_id')
                    ->references('id')->on('clients')
                    ->onDelete('cascade');

            $table->date('reset_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('application_user_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->string('title');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->integer('application_id')->unsigned();

            $table->foreign('application_id')
                ->references('id')->on('applications');
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
        Schema::dropIfExists('applications');
        Schema::dropIfExists('application_user_types');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
