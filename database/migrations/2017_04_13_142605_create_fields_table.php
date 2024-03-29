<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('container_id')->unsigned();
            $table->string('type');
//            $table->('config');
            $table->tinyInteger('status')->default(0);
            
            $table->foreign('container_id')
                    ->references('id')->on('containers')
                    ->onDelete('cascade');
                    
            $table->timestamps();
            $table->softDeletes();
            
        });

        DB::statement("ALTER TABLE fields ADD config MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('fields');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
