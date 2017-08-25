<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class WorkflowEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $status;
    public $mailData;
    public $theuser;
    public $emailTo;
    public $userType;

    /*
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $mailData, $theuser)
    {
        $this->status               = $request->status;
        $this->mailData             = $mailData;
        $this->theuser              = $theuser;

        $this->userType = $theuser->type;
        $this->emailTo = $theuser->email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->emailTo)->send(
            new \App\Mail\WorkflowEmails($this->status, [
                'application_id' => $this->mailData['application_id'],
                'title' => $this->mailData['title'],
                'text' => $this->mailData['text'],
                'allFormsWithErrors' => $this->mailData['allFormsWithErrors'], //only when it's a Approval, it may have a info about latest forms with errors
                'receiverType' => $this->userType,
                'email' => $this->emailTo
            ])
        );

    }
}
