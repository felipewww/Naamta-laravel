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
//            $table->integer('staff_id')->unsigned()->nullable();
            $table->text('description');
            $table->boolean('status')->default(0);

            $table->foreign('client_id')
                    ->references('id')->on('clients')
                    ->onDelete('set null');

//            $table->foreign('staff_id')
//                    ->references('id')->on('application_user_types')
//                    ->onDelete('set null');

            $table->timestamps();
                    
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

        Schema::table('applications', function (Blueprint $table) {
            $table->integer('staff_id')->unsigned()->nullable();
            
            $table->foreign('staff_id')
                ->references('id')->on('application_user_types')
                ->onDelete('set null');
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
