<?php

namespace App\Filament\Resources\PlayerResource\RelationManagers;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContractSignaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'contractSignatures';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('fan_number')
                    ->label('Fan Number')
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->required(),
                Textarea::make('agreed_to_statement')
                    ->label('Agreed to Statement')
                    ->rows(5)
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('signature')
                    ->label('Signature')
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->required(),
                Checkbox::make('agreed_to_statement')
                    ->label('Agreed to Statement')
                    ->dehydrated(true)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('submitted_at')->dateTime(),
                TextColumn::make('contract_name'),
                ImageColumn::make('signature_base64')
                    ->label('Signature')
                    ->getStateUsing(fn($record) => $record->signature_base64 ? $record->signature_base64 : null)
                    ->height(60)
                    ->width(200)
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
        ;
    }
}
