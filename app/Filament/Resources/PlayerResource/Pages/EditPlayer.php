<?php

namespace App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\PlayerResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPlayer extends EditRecord
{
    protected static string $resource = PlayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

            Actions\Action::make('emailContract')
                ->label('Send Contract')
                ->modalHeading('Send Contract')
                ->modalDescription('Do you want to email the contract or sign it now?')
                ->color('gray')
                ->modalFooterActions([
                    Actions\Action::make('sendEmail')
                        ->label('Email')
                        ->color('gray')
                        ->action(function () {
                            // Logic to send the contract via email
                            // Example: $this->record->sendContractEmail();
                            // This would generate the url for the contact and send that
                            // in an email with some instructions
                            // Add a notification and redirect back to the index page

                            // Show a success notification
                            Notification::make()
                                ->title('Contract Sent')
                                ->success()
                                ->body('The contract has been sent to the player via email.')
                                ->send();

                            return redirect(PlayerResource::getUrl('index'));
                        }),

                    Actions\Action::make('signNow')
                        ->label('Sign Now')
                        ->color('gray')
                        ->action(function () {
                            // Open a new window with the url to the sign contract page
                            // with the player id as a query parameter to pre-populate it
                            // return redirect(PlayerResource::getUrl('index'));
                        }),

                    Actions\Action::make('cancel')
                        ->label('Cancel')
                        ->color('secondary')
                ]),

        ];
    }
}
