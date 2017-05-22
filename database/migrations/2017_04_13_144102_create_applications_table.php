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
            $table->enum('status', [0,1,'wt_payment', 'wt_firstform', 'wt_emailconfirm'])->default(0);

            $table->foreign('client_id')
                    ->references('id')->on('clients')
                    ->onDelete('cascade');

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
