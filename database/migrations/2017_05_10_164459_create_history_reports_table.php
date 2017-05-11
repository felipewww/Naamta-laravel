<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('report_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('report_id')
                ->references('id')->on('reports')
                ->onDelete('cascade');
        });

        DB::statement("ALTER TABLE history_reports ADD report_json MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('history_reports');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
