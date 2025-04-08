<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerResource\Pages;
use App\Filament\Resources\PlayerResource\RelationManagers\ContactsRelationManager;
use App\Models\Player;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->columnSpanFull()
                    ->required(),
                DatePicker::make('dob')
                    ->label('Date of Birth')
                    ->maxDate(now())
                    ->required(),
                TextInput::make('fan')
                    ->label('FAN')
                    ->numeric()
                    ->required(),

                TextInput::make('preferred_position'),
                TextInput::make('other_positions'),
                Textarea::make('medical_conditions')
                    ->rows(5)
                    ->columnSpanFull(),
                Textarea::make('injuries')
                    ->rows(5)
                    ->columnSpanFull(),
                Textarea::make('additional_info')->columnSpanFull(),
                Textarea::make('notes')
                    ->rows(10)
                    ->columnSpanFull(),

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
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name}")
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
                TextColumn::make('dob')
                    ->label('DOB')
                    ->date(),
                TextColumn::make('preferred_position')->searchable(),
                TextColumn::make('team.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc');;
    }

    public static function getRelations(): array
    {
        return [
            ContactsRelationManager::class,
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['contacts' => fn($query) => $query->withPivot('is_primary')]);
    }
}
