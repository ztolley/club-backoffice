<?php

namespace App\Http\Controllers;

use App\Mail\PlayerSignedUp;
use App\Models\Contact;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * PublicPlayerSignupFormController
 *
 * This controller handles the public player signup form. This form provides a way for
 * new players offered a position at the club to sign up and provide their details.
 *
 * @package App\Http\Controllers
 */
class PublicPlayerSignupFormController extends Controller
{
    /**
     * Show the form for applying to the club to play
     *
     * Provides a form for the player to fill in their details and submit an application to play for the club.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $teams = Team::orderBy('name')->pluck('name', 'id')->toArray();

        return view('player/signup/form', [
            'teams' => $teams,
        ]);
    }

    /**
     * Store the new player
     *
     * Accepts a form post to a url with the application in it and then creates the appliant record in the database.
     *
     * Create the player record first and then create the contacts before associating the new contacts with the player (the first being the primary contact)
     */
    public function submit(Request $request)
    {
        $playerData = $request->validate([
            'additional_info' => 'nullable|string',
            'agreed_parent_code' => 'boolean',
            'allowed_marketing' => 'nullable|boolean',
            'dob' => 'required|date',
            'fan' => 'required|numeric',
            'injuries' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'name' => 'required|string|max:255',
            'other_positions' => 'nullable|string|max:255',
            'preferred_position' => 'nullable|string|max:255',
            'team_id' => 'nullable|string|max:255',
        ]);

        $playerData['allowed_marketing'] = $playerData['allowed_marketing'] ?? false;
        $playerData['agreed_player_code'] = false;

        // Validate contacts
        $contacts = $request->input('contacts', []);
        foreach ($contacts as $index => $contact) {
            // You can adjust validation rules as needed
            $request->validate([
                "contacts.$index.name" => 'nullable|string|max:255',
                "contacts.$index.email" => 'nullable|email|max:255',
                "contacts.$index.phone" => 'nullable|string|max:255',
                "contacts.$index.address" => 'nullable|string|max:255',
            ]);
        }

        // Create the player record
        $player = Player::create($playerData);

        // Attach contacts to player
        foreach ($contacts as $index => $contactData) {
            // Skip empty contacts (e.g., no name/email/phone)
            if (
                empty($contactData['name'])
            ) {
                continue;
            }

            $contact = Contact::create([
                'name' => $contactData['name'] ?? null,
                'email' => $contactData['email'] ?? null,
                'phone' => $contactData['phone'] ?? null,
                'address' => $contactData['address'] ?? null,
            ]);

            // Attach to player, mark first as primary
            $player->contacts()->attach($contact->id, [
                'is_primary' => $index === 0,
            ]);
        }

        // Send email
        Mail::send(new PlayerSignedUp($player));

        return redirect()->route('player.signup.success');
    }
}
