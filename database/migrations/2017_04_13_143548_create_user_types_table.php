<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('title')->unique();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

//        Schema::create('application_user_types', function (Blueprint $table) {
//            $table->increments('id');
//            $table->string('slug');
//            $table->string('title');
//            $table->tinyInteger('status')->default(1);
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('user_types');
//        Schema::dropIfExists('application_user_types');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}
