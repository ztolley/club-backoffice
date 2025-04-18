<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlayerContract extends Mailable
{
    use Queueable, SerializesModels;

    public $player;
    public $url;

    public function __construct($player)
    {
        $this->player = $player;
        $this->url = env('WWW_URL') . '/player-contract/?player_id=' . $player->id;
    }

    public function build()
    {
        return $this->subject('New Player Contract')
            ->to(env('MAIL_SIGNED_TO_ADDRESS', 'enquiries@hartlandgirlsacademy.co.uk'))
            ->view('emails.player_contract');
    }
}
