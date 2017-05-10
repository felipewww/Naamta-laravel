<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('form_id')
                ->references('id')->on('form_templates')
                ->onDelete('cascade');
        });

        DB::statement("ALTER TABLE history_forms ADD form_json MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('history_forms');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
