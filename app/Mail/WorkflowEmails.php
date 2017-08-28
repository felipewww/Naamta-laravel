<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\User;
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
        $this->wich   = $wich;
        $this->params = $this->checkShortcodesText($params);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $act = $this->wich;
        echo 'ACT:: '.$act;
        return $this->$act();
    }

    public function approved()
    {
        echo "Email step approved sent.\n";
        $this->subject = $this->params['title'];

        view('emails.workflow.templates', ['text' => $this->params['text']]);

        return $this->with([
            'text' => $this->params['text'],
            'allFormsWithErrors' => $this->params['allFormsWithErrors'],
        ])->view('emails.workflow.templates');
    }

    public function rejected()
    {
        echo "Email step reject sent.\n";
        $this->subject = $this->params['title'];

        view('emails.workflow.templates', ['text' => $this->params['text']]);

        return $this->with([
            'text' => $this->params['text'],
            'allFormsWithErrors' => $this->params['allFormsWithErrors'],
        ])->view('emails.workflow.templates');
    }

    private function checkShortcodesText($params){
        //dd($params);
        //echo 'shortcodes';
        $application = Application::with(["client"])->find($params['application_id']);
        $client = $application->client;

        if ( $params['receiverType'] == 'just_receiver' ){
//            echo 'Just receiver: '.$params['email'];
            $params['text'] = str_replace("[ClientName]", "Responsible for ".$client->company." application", $params['text']);
            $params['text'] = str_replace("[ClientEmail]", $params['email'], $params['text']);
            $params['text'] = str_replace("[ClientCompany]", $client->company, $params['text']);
        }else{
            $user   = User::find($client->user_id);

            $params['text'] = str_replace("[ClientName]", $user->name, $params['text']);
            $params['text'] = str_replace("[ClientEmail]", $user->email, $params['text']);
            $params['text'] = str_replace("[ClientCompany]", $client->company, $params['text']);
        }
        return $params;
    }
}
