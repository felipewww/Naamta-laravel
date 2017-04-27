<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FormTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('form_templates')->insert(
            array
            (
                [
                    'id'        => 1,
                    'name'     => 'Form Template One',
                    'status'    => 1,
                    'created_at' => Carbon::now(-3),
                    'updated_at' => Carbon::now(-3),
                ],
                [
                    'id'        => 2,
                    'name'     => 'Form Template Two',
                    'status'    => 1,
                    'created_at' => Carbon::now(-2),
                    'updated_at' => Carbon::now(-2),
                ],
                [
                    'id'        => 3,
                    'name'     => 'Form Template Three',
                    'status'    => 1,
                    'created_at' => Carbon::now(3),
                    'updated_at' => Carbon::now(3),
                ],
            )
        );
    }
}
