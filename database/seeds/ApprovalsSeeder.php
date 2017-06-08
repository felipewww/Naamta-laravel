<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ApprovalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('approvals')->insert(
            array
            (
                [
                    'id'                => 1,
                    'title'             => 'Approval Template One',
                    'description'       => Faker::create()->paragraph(1),
                    'has_report'        => 1
                ],
                [
                    'id'                => 2,
                    'title'             => 'Approval Template two',
                    'description'       => Faker::create()->paragraph(1),
                ],
                [
                    'id'                => 3,
                    'title'             => 'Approval Template Three',
                    'description'       => Faker::create()->paragraph(1),
                    'has_report'        => 1
                ],
            )
        );
    }
}
