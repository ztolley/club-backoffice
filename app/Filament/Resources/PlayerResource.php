<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerResource\Pages;
use App\Models\Player;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

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
                Textarea::make('parent_carer_names')
                    ->label('Parent/Carer Names')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('preferred_position'),
                TextInput::make('other_positions'),
                Textarea::make('medical_conditions')->columnSpanFull(),
                Textarea::make('injuries')->columnSpanFull(),
                Textarea::make('additional_info')->columnSpanFull(),

                Toggle::make('allowed_marketing')->required(),
                Toggle::make('allowed_photography')->required(),

                Toggle::make('agreed_player_code')
                    ->label('Agreed to Player Code of Conduct')
                    ->required(),
                Toggle::make('agreed_parent_code')
                    ->label('Agreed to Parent Code of Conduct')
                    ->required(),

                Select::make('team_id')->relationship('team', 'name'),

                DatePicker::make('signed_date'),

                Select::make('applicant_id')
                    ->relationship('applicant', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('dob')
                    ->label('DOB')
                    ->date(),
                TextColumn::make('preferred_position')->searchable(),
                TextColumn::make('team.name')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListPlayers::route('/'),
            'create' => Pages\CreatePlayer::route('/create'),
            'edit' => Pages\EditPlayer::route('/{record}/edit'),
        ];
    }
}
