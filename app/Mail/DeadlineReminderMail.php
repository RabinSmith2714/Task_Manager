<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class DeadlineReminderMail extends Mailable
{
    public $details;

    /**
     * Create a new message instance.
     *
     * @param array $details
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Deadline Reminder')
                    ->view('deadlineReminder')  // Reference the view directly
                    ->with('details', $this->details);
    }
}
