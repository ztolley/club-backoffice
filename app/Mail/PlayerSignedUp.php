<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlayerSignedUp extends Mailable
{
    use Queueable, SerializesModels;

    public $player;
    public $team;
    public $email;

    public function __construct($player, $team, $email = null)
    {
        $this->player = $player;
        $this->team = $team;
        $this->email = $email;
    }

    public function build()
    {
        return $this->subject('New Player - Signed Up')
            ->to(env('MAIL_SIGNED_TO_ADDRESS', 'trials@hartlandgirlsacademy.co.uk'))
            ->view('emails.player_submitted');
    }
}
