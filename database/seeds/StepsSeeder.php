<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class StepsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id'                => 1,
                'morphs_from'       => \App\Models\FormTemplate::class,
                'responsible'       => 4,
                'status'            => 1,
                'title'             => 'Step A',
                'previous_step'     => null,
                'ordination'        => 0,
                'description'       => Faker::create()->paragraph(1),
            ],
            [
                'id'                => 2,
                'morphs_from'       => \App\Models\Approval::class,
                'responsible'       => 1,
                'status'            => 1,
                'title'             => 'Step B [screen]',
                'previous_step'     => 1,
                'ordination'        => 1,
                'description'       => Faker::create()->paragraph(1),
                'morphs_id'         => 1
            ],
            [
                'id'                => 3,
                'morphs_from'       => \App\Models\FormTemplate::class,
                'responsible'       => 4,
                'status'            => 1,
                'title'             => 'Step C',
                'previous_step'     => 2,
                'ordination'        => 2,
                'description'       => Faker::create()->paragraph(1),
            ],
            [
                'id'                => 4,
                'morphs_from'       => \App\Models\Approval::class,
                'responsible'       => 1,
                'status'            => 1,
                'title'             => 'Step D [screen]',
                'previous_step'     => 3,
                'ordination'        => 3,
                'description'       => Faker::create()->paragraph(1),
                'morphs_id'         => 2
            ],
            [
                'id'                => 5,
                'morphs_from'       => \App\Models\FormTemplate::class,
                'responsible'       => 4,
                'status'            => 1,
                'title'             => 'Step E',
                'previous_step'     => 4,
                'ordination'        => 4,
                'description'       => Faker::create()->paragraph(1),
            ],
            [
                'id'                => 6,
                'morphs_from'       => \App\Models\Approval::class,
                'responsible'       => 4,
                'status'            => 1,
                'title'             => 'Step F [screen]',
                'previous_step'     => 5,
                'ordination'        => 5,
                'description'       => Faker::create()->paragraph(1),
                'morphs_id'         => 3
            ]
        ];

        foreach ($data as $fake)
        {
            $newFake = \App\Models\Step::create($fake);
        }
    }
}
