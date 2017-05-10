<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('approval_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('approval_id')
                ->references('id')->on('approvals')
                ->onDelete('cascade');
        });

        DB::statement("ALTER TABLE reports ADD form MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('reports');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
