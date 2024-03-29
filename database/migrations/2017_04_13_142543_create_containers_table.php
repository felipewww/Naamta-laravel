<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_template_id')->unsigned();
            $table->string('name');
            $table->string('config');
            
            $table->foreign('form_template_id')
                    ->references('id')->on('form_templates')
                    ->onDelete('cascade');
                    
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('containers');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
