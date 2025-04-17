<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForwardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $forwardtaskDetails;

    public function __construct($forwardtaskDetails)
    {
        $this->forwardtaskDetails = $forwardtaskDetails;
    }

    public function build()
    {
        return $this->subject('Task Forwarded')
            ->view('task_forwarded')
            ->with('taskDetails', $this->forwardtaskDetails);
    }
}
