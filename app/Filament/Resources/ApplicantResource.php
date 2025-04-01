<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantResource\Pages;
use App\Models\Applicant;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                TextInput::make('name')->required(),
                Textarea::make('address')
                    ->required(),
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->required(),
                DatePicker::make('dob')
                    ->label('Date of Birth')
                    ->maxDate(now())
                    ->required(),
                TextInput::make('school'),
                TextInput::make('saturday_club'),
                TextInput::make('sunday_club'),
                Textarea::make('previous_clubs')->columnSpanFull(),
                Textarea::make('playing_experience')->columnSpanFull(),
                TextInput::make('preferred_position'),
                TextInput::make('other_positions'),
                TextInput::make('age_groups'),
                Textarea::make('how_hear')
                    ->label('How did you hear about us?')
                    ->columnSpanFull(),
                Textarea::make('medical_conditions')->columnSpanFull(),
                Textarea::make('injuries')->columnSpanFull(),
                Textarea::make('additional_info')->columnSpanFull(),
                DatePicker::make('application_date')
                    ->label('Application Date')
                    ->default(now()),
                Textarea::make('notes')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('dob')
                    ->label('DOB')
                    ->date()
                    ->sortable(),
                TextColumn::make('preferred_position')->searchable(),
                TextColumn::make('age_groups')
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc');
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
