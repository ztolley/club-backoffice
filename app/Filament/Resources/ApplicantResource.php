<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantResource\Pages;
use App\Models\Applicant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApplicantResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->required(),
                Forms\Components\TextInput::make('last_name')
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->required(),
                Forms\Components\TextInput::make('postal_code')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required(),
                Forms\Components\DatePicker::make('dob')
                    ->required(),
                Forms\Components\TextInput::make('school'),
                Forms\Components\TextInput::make('saturday_club'),
                Forms\Components\TextInput::make('sunday_club'),
                Forms\Components\Textarea::make('previous_clubs')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('playing_experience')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('preferred_position'),
                Forms\Components\TextInput::make('other_positions'),
                Forms\Components\TextInput::make('age_groups'),
                Forms\Components\Textarea::make('how_hear')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('medical_conditions')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('injuries')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('additional_info')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('postal_code')->searchable(),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('dob')
                    ->date()
                    ->sortable(),
                TextColumn::make('preferred_position')->searchable(),
                TextColumn::make('age_groups')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplicants::route('/'),
            'create' => Pages\CreateApplicant::route('/create'),
            'edit' => Pages\EditApplicant::route('/{record}/edit'),
        ];
    }
}
