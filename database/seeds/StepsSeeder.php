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
                'responsible'       => 1,
                'status'            => 1,
                'title'             => 'Step A',
                'previous_step'     => null,
                'description'       => Faker::create()->paragraph(1),
            ],
            [
                'id'                => 2,
                'morphs_from'       => \App\Models\Screens::class,
                'responsible'       => 1,
                'status'            => 1,
                'title'             => 'Step B [screen]',
                'previous_step'     => 1,
                'description'       => Faker::create()->paragraph(1),
            ],
            [
                'id'                => 3,
                'morphs_from'       => \App\Models\FormTemplate::class,
                'responsible'       => 1,
                'status'            => 1,
                'title'             => 'Step C',
                'previous_step'     => 2,
                'description'       => Faker::create()->paragraph(1),
            ],
            [
                'id'                => 4,
                'morphs_from'       => \App\Models\Screens::class,
                'responsible'       => 1,
                'status'            => 1,
                'title'             => 'Step D [screen]',
                'previous_step'     => 3,
                'description'       => Faker::create()->paragraph(1),
            ],
            [
                'id'                => 5,
                'morphs_from'       => \App\Models\FormTemplate::class,
                'responsible'       => 1,
                'status'            => 1,
                'title'             => 'Step E',
                'previous_step'     => 4,
                'description'       => Faker::create()->paragraph(1),
            ],
            [
                'id'                => 6,
                'morphs_from'       => \App\Models\Screens::class,
                'responsible'       => 1,
                'status'            => 0,
                'title'             => 'Step F [screen]',
                'previous_step'     => 5,
                'description'       => Faker::create()->paragraph(1),
            ]
        ];

        foreach ($data as $fake)
        {
            $newFake = \App\Models\Step::create($fake);
        }
    }
}
