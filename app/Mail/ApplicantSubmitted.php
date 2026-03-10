<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicantSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;
    public $applicationType;

    public function __construct($applicant, string $applicationType = 'player')
    {
        $this->applicant = $applicant;
        $this->applicationType = $applicationType;
    }

    public function build()
    {
        return $this->subject($this->applicationType === 'etp'
            ? 'New Emerging Talent Programme Application Submitted'
            : 'New Player Application Submitted')
            ->to(config('mail.application_to_address'))
            ->view('emails.applicant_submitted');
    }
}
