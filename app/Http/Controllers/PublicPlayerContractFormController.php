<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PlayerContractSignature;
use Illuminate\Http\Request;

class PublicPlayerContractFormController extends Controller
{
    protected string $playerPromise = <<<HTML
<p>As an Hartland Girls Academy player, I will…</p>

<ul class="wp-block-list">
    <li>Be punctual for training and matches.</li>
    <li>Be prepared by showing up with correct kit, equipment and footwear.</li>
    <li>Always play and train at my best for the benefit of the team and teammates.</li>
    <li>Play fairly – I won’t cheat, dive, complain or waste time.</li>
    <li>Respect the facilities and equipment, both home and away.</li>
    <li>Offer my support and gratitude to the opposition – win or lose.</li>
    <li>Listen and respond to my coach’s instructions/decisions.</li>
    <li>Talk to someone at the academy who I trust if I am unhappy or worried about any of my team mates.</li>
    <li>Enjoy my football and adopt a positive outlook and approach to playing.</li>
    <li>Be mindful and respectful of how I talk about football activity and the academy on social media.</li>
    <li>RESPECT all on the pitch – team-mates, opposition, the officials and the team coaches.</li>
    <li>Help promote a safe, friendly environment in which every player can enjoy their football.</li>
</ul>
HTML;

    /**
     * Show the form for signing the player contract.
     *
     * Fetches the player record using their ID and passes it to a form that will render their
     * name and fan alongside the player promise and collect a user signature
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        $player = Player::where('id', $id)->firstOrFail();

        return view('player-contract-form', [
            'player' => $player,
            'playerPromise' => $this->playerPromise
        ]);
    }

    /**
     * Store the player contract signature.
     *
     * Accepts a form post to a url with the player ID within it. The signature it send as a base64 string to represent
     * a png.
     *
     * The signature along with the player name, fan number and agreed to content are stored in the database.
     */
    public function submit(Request $request, string $id)
    {
        $player = Player::where('id', $id)->firstOrFail();

        $data = $request->validate([
            'signature' => 'required|string', // base64 PNG
        ]);

        PlayerContractSignature::create([
            'player_id' => $player->id,
            'name' => $player->name,
            'fan' => $player->fan,
            'signature_base64' => $data['signature'],
            'contract_content' => $this->playerPromise,
            'submitted_at' => now(),
        ]);

        return redirect()->route('player.contract.success');
    }
}
