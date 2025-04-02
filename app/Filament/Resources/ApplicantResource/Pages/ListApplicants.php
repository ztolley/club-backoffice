<?php

namespace App\Filament\Resources\ApplicantResource\Pages;

use App\Actions\IngestApplicants;
use App\Filament\Resources\ApplicantResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListApplicants extends ListRecords
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('ingest')
                ->label('Ingest Applicants')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    app(IngestApplicants::class)->handle();

                    Notification::make()
                        ->title('Applicants ingested successfully!')
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
