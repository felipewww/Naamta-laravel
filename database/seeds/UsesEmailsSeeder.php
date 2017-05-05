<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsesEmailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('uses_emails')->insert(
            array
            (
                [
//                    'id'            => 1,
                    'email_id'      => 1,
                    'received_by'   => 1,
                    'step_id'       => 1,
                    'send_when'     => 'success',
                    'created_at' => Carbon::now(-5),
                    'updated_at' => Carbon::now(),
                ],
                [
//                    'id'            => 2,
                    'email_id'      => 2,
                    'received_by'   => 1,
                    'step_id'       => 1,
                    'send_when'     => 'rejected',
                    'created_at' => Carbon::now(-5),
                    'updated_at' => Carbon::now(),
                ],
            )
        );
    }
}
