<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;

class EmailTemplates extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('email_templates')->insert(
            array
            (
                [
                    'id'        => 1,
                    'title'     => 'Email Template One',
                    'text'     => Faker::create()->paragraph(1),
                    'status'    => 1,
                    'created_at' => Carbon::now(-3),
                    'updated_at' => Carbon::now(-3),
                ],
                [
                    'id'        => 2,
                    'title'     => 'Email Template Two',
                    'text'     => Faker::create()->paragraph(1),
                    'status'    => 1,
                    'created_at' => Carbon::now(-2),
                    'updated_at' => Carbon::now(-2),
                ],
                [
                    'id'        => 3,
                    'title'     => 'Email Template Three',
                    'text'     => Faker::create()->paragraph(1),
                    'status'    => 1,
                    'created_at' => Carbon::now(3),
                    'updated_at' => Carbon::now(3),
                ],
            )
        );
    }
}
