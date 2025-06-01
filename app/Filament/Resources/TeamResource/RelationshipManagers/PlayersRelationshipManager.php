<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use App\Services\RichTextEmailSender;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Collection;

class PlayersRelationManager extends RelationManager
{
    protected static string $relationship = 'players';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('primaryContact.email')
                    ->label('Email')
                    ->getStateUsing(function ($record) {
                        return $record->contacts
                            ->firstWhere('pivot.is_primary', true)?->email;
                    }),
                TextColumn::make('primaryContact.phone')
                    ->label('Phone')
                    ->getStateUsing(function ($record) {
                        return $record->contacts
                            ->firstWhere('pivot.is_primary', true)?->phone;
                    }),
                TextColumn::make('preferred_position')->searchable(),
            ])
            ->recordUrl(fn($record) => route('filament.admin.resources.players.edit', $record))
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),

                    BulkAction::make('sendEmail')
                        ->label('Email primary contacts')
                        ->icon('heroicon-m-envelope')
                        ->form([
                            TextInput::make('subject')
                                ->required()
                                ->label('Subject'),

                            RichEditor::make('body')
                                ->required()
                                ->label('Email Body'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            // Each record is a Player model
                            // Get the primary contact for each player to get the email addresses
                            // the contact returned in the array must have the email attribute
                            $filtered = $records->map(function ($record) {
                                return $record->contacts
                                    ->firstWhere('pivot.is_primary', true);
                            })
                                ->filter(fn($record) => !empty($record->email))
                                ->unique();


                            if ($filtered->isEmpty()) {
                                Notification::make()
                                    ->title('No valid email addresses found.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            app(RichTextEmailSender::class)
                                ->sendToMany($filtered, $data['subject'], $data['body']);

                            Notification::make()
                                ->title('Email sent.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->modalHeading('Compose Email')
                        ->modalSubmitActionLabel('Send'),
                ]),
            ])
            ->defaultSort('name', 'asc');;;
    }
}
