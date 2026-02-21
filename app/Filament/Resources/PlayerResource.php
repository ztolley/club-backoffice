<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\PlayerResource\Pages;
use App\Filament\Resources\PlayerResource\RelationManagers\ContactsRelationManager;
use App\Filament\Resources\PlayerResource\RelationManagers\ContractSignaturesRelationManager;
use App\Models\Player;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
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

                Toggle::make('allowed_marketing')
                    ->columnSpanFull()
                    ->required(),

                Toggle::make('agreed_player_code')
                    ->label('Agreed to Player Code of Conduct')
                    ->required(),
                Toggle::make('agreed_parent_code')
                    ->label('Agreed to Parent Code of Conduct')
                    ->required(),

                Select::make('team_id')
                    ->label('Primary Team')
                    ->relationship('team', 'name', fn($query) => $query->orderBy('name'))
                    ->nullable()
                    ->preload()
                    ->reactive(),

                Select::make('alternate_team_id')
                    ->label('Alternate Team')
                    ->relationship('alternateTeam', 'name')
                    ->nullable()
                    ->preload()
                    ->helperText('Optional. The second team this player is registered with.')
                    ->reactive()
                    ->options(function ($get) {
                        $primary = $get('team_id');
                        return \App\Models\Team::when($primary, fn($query) => $query->where('id', '!=', $primary))
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    }),

                DatePicker::make('signed_date'),
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
                    ->toggleable()
                    ->getStateUsing(function ($record) {
                        return $record->contacts
                            ->firstWhere('pivot.is_primary', true)?->email;
                    }),
                TextColumn::make('primaryContact.phone')
                    ->label('Phone')
                    ->toggleable()
                    ->getStateUsing(function ($record) {
                        return $record->contacts
                            ->firstWhere('pivot.is_primary', true)?->phone;
                    }),
                TextColumn::make('dob')
                    ->label('DOB')
                    ->date()
                    ->toggleable(),
                TextColumn::make('preferred_position')->searchable()->toggleable(),
                TextColumn::make('team.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->recordUrl(fn($record) => route('filament.admin.resources.players.edit', $record))
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('team_id')
                    ->label('Team')
                    ->options(\App\Models\Team::orderBy('name')->pluck('name', 'id')->toArray())
                    ->indicateUsing(function ($state): ?string {
                        if (!$state || !is_numeric($state)) return null;
                        $team = \App\Models\Team::find($state);
                        return $team ? 'Team: ' . $team->name : null;
                    }),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            ContactsRelationManager::class,
            ContractSignaturesRelationManager::class,
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
