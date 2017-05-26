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
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->theuser)->send(
            new \App\Mail\WorkflowEmails($this->status, [
                'title' => $this->mailData['title'],
                'text' => $this->mailData['text']
            ])
        );
    }
}
