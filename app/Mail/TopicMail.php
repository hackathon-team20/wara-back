<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TopicMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $emailBody;

    public function __construct($subject, $emailBody)
    {
        $this->subject = $subject;
        $this->emailBody = $emailBody;
    }

    public function build()
    {
        return $this->view('emails.topic')
                    ->subject($this->subject)
                    ->with('emailBody', $this->emailBody);
    }
}
