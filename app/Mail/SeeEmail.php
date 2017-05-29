<?php

namespace App\Mail;

use Faker\Factory as Faker;

class SeeEmail {

    public static function see($email){
        $view = '';
        $params = [];
        $f = new Faker();
        $faker = $f->create();

        switch ($email)
        {
            case 'allow_app':
                $view = 'emails.Auth.allow_app';
                $client = \App\Models\Client::inRandomOrder()->first();
                $user = $client->user;

                $params = [
                    'client' => $client,
                    'user' => $user
                ];

                break;

            case 'deny_app':
                $view = 'emails.Auth.deny_app';
                $client = \App\Models\Client::inRandomOrder()->first();
                $user = $client->user;

                $params = [
                    'client' => $client,
                    'user' => $user
                ];
                break;

            case 'reset_app':
                $view = 'emails.Auth.reset_app';
                $client = \App\Models\Client::inRandomOrder()->first();
                $user = $client->user;

                $params = [
                    'client' => $client,
                    'user' => $user
                ];
                break;

            case 'templates': //WORKFLOW STEPS
                $view = 'emails.workflow.templates';
                $data = \App\Models\EmailTemplate::inRandomOrder()->first();
                $params = [
                    'text' => $data['text']
                ];
                break;

            case 'register':
                $view = 'emails.Auth.register';
                $params['token']    = $faker->sha256;
                $params['user']     = \App\Models\User::inRandomOrder()->first();
                break;
        }

        return view($view, $params)->withShortcodes();
    }
}