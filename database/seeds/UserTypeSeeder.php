<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$faker = Faker::create();
        DB::table('user_types')->insert(
            array
            (
                [
                    'id'        => 1,
                    'slug'      => 'stafftype-1',
                    'title'     => 'Staff type 1',
                    'status'    => 1,
                    'created_at' => Carbon::now(-5),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id'        => 2,
                    'slug'      => 'stafftype-2',
                    'title'     => 'Staff type 2',
                    'status'    => 1,
                    'created_at' => Carbon::now(-5),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id'        => 3,
                    'slug'      => 'stafftype-3',
                    'title'     => 'Staff type 3',
                    'status'    => 1,
                    'created_at' => Carbon::now(-5),
                    'updated_at' => Carbon::now(),
                ],
            )
        );
    }
}
