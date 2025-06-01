<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerResource\Pages;
use App\Filament\Resources\PlayerResource\RelationManagers\ContactsRelationManager;
use App\Filament\Resources\PlayerResource\RelationManagers\ContractSignaturesRelationManager;
use App\Models\Player;
use App\Services\RichTextEmailSender;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
            ->defaultSort('name', 'asc');;
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
