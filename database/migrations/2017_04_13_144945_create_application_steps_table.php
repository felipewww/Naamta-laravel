<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_steps', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title');
            $table->string('description');
//            $table->tinyInteger('status')->default(0);
            $table->enum('status', ['0','1','approved','reproved','current']);
            $table->integer('ordination');
            $table->integer('responsible')->unsigned();
            $table->integer('application_id')->unsigned();
            $table->integer('previous_step')->unsigned()->nullable();

            $table->string('morphs_from');
//            $table->integer('morphs_id')->nullable();
//
//            $table->foreign('morphs_id')
//                ->references('id')->on('approvals');

            $table->foreign('application_id')
                ->references('id')->on('applications')
                ->onDelete('cascade');
            
            $table->foreign('previous_step')
                ->references('id')->on('application_steps');
            
            $table->foreign('responsible')
                    ->references('id')->on('application_user_types')
                    ->onDelete('restrict');
            $table->timestamps();
        });

//        DB::statement("ALTER TABLE application_steps ADD morphs_json MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('application_steps');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
