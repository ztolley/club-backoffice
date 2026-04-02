<?php

namespace App\Http\Controllers;

use App\Mail\ApplicantSubmitted;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PublicPlayerApplicationFormController extends Controller
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
        return $this->showForm('player');
    }

    public function showEtp()
    {
        return $this->showForm('etp');
    }

    /**
     * Store the application
     *
     * Accepts a form post to a url with the application in it and then creates the appliant record in the database.
     */
    public function submit(Request $request)
    {
        return $this->submitForm($request, 'player');
    }

    public function submitEtp(Request $request)
    {
        return $this->submitForm($request, 'etp');
    }

    protected function showForm(string $type)
    {
        return view('application/form', [
            'applicationType' => $type,
        ]);
    }

    protected function submitForm(Request $request, string $type)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'dob' => 'required|date',
            'school' => 'nullable|string|max:255',
            'saturday_club' => 'nullable|string|max:255',
            'sunday_club' => 'nullable|string|max:255',
            'previous_clubs' => 'nullable|string',
            'playing_experience' => 'nullable|string',
            'preferred_position' => 'nullable|string|max:255',
            'other_positions' => 'nullable|string|max:255',
            'preferred_foot' => 'nullable|in:Right,Left,Both',
            'age_groups' => 'nullable|string|max:255',
            'how_hear' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'injuries' => 'nullable|string',
            'additional_info' => 'nullable|string',
        ]);

        if ($type === 'etp') {
            $data['age_groups'] = 'ETP';
        }

        $applicant = Applicant::create($data);

        // Send email
        Mail::send(new ApplicantSubmitted($applicant, $type));

        return redirect()->route($type === 'etp' ? 'etp.application.success' : 'application.success');
    }
}
