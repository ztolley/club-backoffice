<?php

namespace App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\PlayerResource;
use App\Models\Applicant;
use Filament\Resources\Pages\CreateRecord;

class CreatePlayer extends CreateRecord
{
    protected static string $resource = PlayerResource::class;

    // When the Create Player screen is loaded check to see if it was called as a result of the 'Add Player To Club'
    // action in the applicant screen. This happens when an applicant is success at a trial and is now going to be
    // added to the club as a player. If so, pre-fill the player's details with the details from the application form
    // and link the player record to the application.
    public function mount(): void
    {
        parent::mount();

        $applicantId = request()->get('applicant_id');

        if ($applicantId) {
            $applicant = Applicant::find($applicantId);

            if ($applicant) {
                $this->form->fill([
                    'name' => $applicant->name,
                    'dob' => $applicant->dob,
                    'applicant_id' => $applicant->id,
                ]);
            }
        }
    }
}
