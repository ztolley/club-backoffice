<?php

namespace App\Filament\Resources\ApplicantResource\Pages;

use App\Filament\Resources\ApplicantResource;
use App\Filament\Resources\PlayerResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditApplicant extends EditRecord
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('rejectApplication')
                ->label('Reject Application')
                ->modalHeading('Reject Application')
                ->modalDescription('Are you sure you want to reject this application? An email will be sent to the applicant informing them of the decision.')
                ->modalCancelActionLabel('Cancel')
                ->modalSubmitActionLabel('Yes, Reject Application')
                ->color('gray')
                ->action(function () {
                    // Reject the application
                    // $this->record->reject();

                    Notification::make()
                        ->title('Rejection sent')
                        ->success()
                        ->body('The application has been notified by email.')
                        ->send();
                }),

            Actions\Action::make('acceptApplication')
                ->label('Accept Application')
                ->modalHeading('Reject Application')
                ->modalDescription('Are you sure you want to accept this application? An email will be sent to the applicant informing them of the decision.')
                ->modalCancelActionLabel('Cancel')
                ->modalSubmitActionLabel('Yes, Accept Application')
                ->color('warning')
                ->action(function () {
                    // Reject the application
                    // $this->record->accept();

                    Notification::make()
                        ->title('Acceptance sent')
                        ->success()
                        ->body('The application has been notified by email.')
                        ->send();
                }),

            Actions\Action::make('addPlayerToClub')
                ->label('Add Player to Club')
                ->modalHeading('Add Player to Club')
                ->modalDescription('Are you sure you want to add this applicant as a squad member?')
                ->modalSubmitActionLabel('Yes, Add Player')
                ->color('success')
                ->action(function () {
                    // Redirect to the Create Player screen with pre-filled data
                    return redirect(PlayerResource::getUrl('create', [
                        'applicant_id' => $this->record->id,
                    ]));
                }),

            Actions\DeleteAction::make()->color('danger'),
        ];
    }
}
