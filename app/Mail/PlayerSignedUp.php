<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlayerSignedUp extends Mailable
{
    use Queueable, SerializesModels;

    public $player;

    public function __construct($player)
    {
        $this->player = $player;
    }

    public function build()
    {
        return $this->subject('New PlayerSigned Up')
            ->to(env('MAIL_SIGNED_TO_ADDRESS', 'trials@hartlandgirlsacademy.co.uk'))
            ->view('emails.player_submitted');
    }
}
