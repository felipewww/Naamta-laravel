<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Webwizo\Shortcodes\Compilers\Shortcode;
use App\Shortcodes;

class WorkflowEmails extends Mailable
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
        $this->params   = $this->checkShortcodesText($params);
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

    public function approved()
    {
        echo "Email approving an step sent.\n";
        $this->subject = $this->params['title'];

        view('emails.workflow.templates', ['text' => $this->params['text']]);

        return $this->with([
            'text' => $this->params['text'],
            'allFormsWithErrors' => $this->params['allFormsWithErrors'],
        ])
            ->view('emails.workflow.templates');
//            ->view(['html' => 'emails.workflow.templates']);

    }

    public function reproved()
    {
        $this->subject = $this->params['title'];

        view('emails.workflow.templates', ['text' => $this->params['text']]);

        return $this->with([
            'text' => $this->params['text'],
            'allFormsWithErrors' => $this->params['allFormsWithErrors'],
        ])
            ->view('emails.workflow.templates');
    }

    private function checkShortcodesText($params){
        $client = $params->application->getClient();
        $user = User::find($client->user_id);

        $ret = str_replace("[ClientName]", $user->name, $params['text']);
        $ret = str_replace("[ClientEmail]", $user->email, $ret);
        $ret = str_replace("[ClientCompany]", $client->company, $ret);

        return $ret;
    }
}
