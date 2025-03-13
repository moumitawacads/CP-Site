<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LearnerLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $type;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $type = null)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
            ->subject($this->type ? 'Clinician Learner Link' : 'Learner Link Created')
            ->view('emails.learner_link')
            ->with(['data' => $this->data, 'type' => $this->type]);
    }
}
