<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicantSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;

    public function __construct($applicant)
    {
        $this->applicant = $applicant;
    }

    public function build()
    {
        return $this->subject('New Player Application Submitted')
            ->to(env('MAIL_APPLICATION_TO_ADDRESS', 'trials@hartlandgirlsacademy.co.uk'))
            ->view('emails.applicant_submitted');
    }
}
