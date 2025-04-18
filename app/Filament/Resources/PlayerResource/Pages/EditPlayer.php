<?php

namespace App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\PlayerResource;
use App\Mail\PlayerContract;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditPlayer extends EditRecord
{
    protected static string $resource = PlayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

            ActionGroup::make([
                Actions\Action::make('sendEmail')
                    ->label('Email Player Contract')
                    ->icon('heroicon-m-envelope')
                    ->action(function () {
                        Mail::send(new PlayerContract($this->record));

                        // Show a success notification
                        Notification::make()
                            ->title('Contract Sent')
                            ->success()
                            ->body('The contract has been sent to the player via email.')
                            ->send();
                    }),

                Actions\Action::make('signNow')
                    ->label('Sign Player Contact Now')
                    ->icon('heroicon-s-window')
                    ->url(fn($record) => route('player.contract.show', $record->id))
                    ->openUrlInNewTab(),

            ]),

        ];
    }
}
