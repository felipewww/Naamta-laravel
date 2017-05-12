<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description');
            $table->tinyInteger('has_report')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('steps', function (Blueprint $table) {
            $table->integer('morphs_id')->unsigned()->nullable();

            $table->foreign('morphs_id')
                ->references('id')->on('approvals')
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
        Schema::dropIfExists('approvals');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
