<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use Filament\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

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
                ]),
            ])
            ->defaultSort('name', 'asc');;;
    }
}
