<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('screens')->insert(
            array
            (
                [
                    'id'                => 1,
                    'title'             => 'Screen One',
                    'description'       => Faker::create()->paragraph(1),
                ],
                [
                    'id'                => 2,
                    'title'             => 'Screen two',
                    'description'       => Faker::create()->paragraph(1),
                ],
                [
                    'id'                => 3,
                    'title'             => 'Screen Three',
                    'description'       => Faker::create()->paragraph(1),
                ],
            )
        );
    }
}
