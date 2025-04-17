<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $taskDetails;

    public function __construct($taskDetails)
    {
        $this->taskDetails = $taskDetails;
    }

    public function build()
    {
        return $this->subject('New Task Assigned')
            ->view('task_added')
            ->with('taskDetails', $this->taskDetails);
    }
}
