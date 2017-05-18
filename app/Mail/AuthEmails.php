<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuthEmails extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $wich;
    public $params;
    public $subject = 'Register subject';
    public $from = [
        [   'name' => 'Naamta',
            'address' => 'naamta@blanko.be'
        ]
    ];

    public $replyTo = [
        [   'name' => 'Naamta',
            'address' => 'naamta@blanko.be'
        ]
    ];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($wich, Array $params)
    {
        $this->wich     = $wich;
        $this->params   = $params;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $act = $this->wich;
        return $this->$act();
    }

    public function register()
    {
        $token  = $this->params['token'];
        $user   = $this->params['user'];

        return $this->with([
            'token' => $token,
            'user' => $user
            ])
            ->view('emails.Auth.register');
    }
}
