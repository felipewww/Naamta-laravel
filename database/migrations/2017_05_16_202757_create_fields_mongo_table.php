<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsMongoTable extends Migration
{
    /**
     * The name of the database connection to use.
     *
     * @var string
     */
    protected $connection = 'mongodb';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::connection($this->connection)
            ->table('fields', function (Blueprint $collection)
            {
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        Schema::connection($this->connection)
            ->table('fields', function (Blueprint $collection)
            {
                $collection->drop();
            });
    }
}
