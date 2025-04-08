<?php

namespace App\Filament\Resources\PlayerResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('email')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                Textarea::make('address')
                    ->rows(5)
                    ->columnSpanFull(),
                Checkbox::make('is_primary')
                    ->label('Primary Contact')
                    ->afterStateHydrated(function (\Filament\Forms\Set $set, $record) {
                        $set('is_primary', (bool) ($record?->pivot?->is_primary ?? false));
                    })
                    ->dehydrated(true)
                    ->saveRelationshipsUsing(function ($record, $state) {
                        $record->pivot->is_primary = $state;
                        $record->pivot->save();
                    })
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('phone'),
                IconColumn::make('pivot.is_primary')
                    ->boolean()
                    ->label('Primary')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon(''),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
