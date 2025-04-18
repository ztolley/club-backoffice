<?php

namespace App\Filament\Resources\PlayerResource\RelationManagers;

use App\Services\RichTextEmailSender;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

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
                    ->email()
                    ->columnSpanFull(),
                // Email action button
                Forms\Components\Actions::make([
                    FormAction::make('sendEmail')
                        ->icon('heroicon-m-envelope')
                        ->label('')
                        ->color('success')
                        ->size('sm')
                        ->tooltip('Send email to contact')
                        ->form([
                            TextInput::make('subject')->required()->label('Subject'),
                            RichEditor::make('body')->required()->label('Email Body'),
                        ])
                        ->action(function (array $data, $record) {
                            if (empty($record->email)) {
                                Notification::make()
                                    ->title('No email address for this contact.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            app(\App\Services\RichTextEmailSender::class)
                                ->sendToMany(collect([$record]), $data['subject'], $data['body']);

                            Notification::make()
                                ->title('Email sent.')
                                ->success()
                                ->send();
                        })
                        ->modalHeading('Compose Email')
                        ->modalSubmitActionLabel('Send')
                        ->hidden(function ($get) {
                            return blank($get('email'));
                        }),
                ]),
                TextInput::make('phone')
                    ->tel()
                    ->columnSpanFull(),
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
                    }),


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
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),

                    BulkAction::make('sendEmail')
                        ->label('Email contacts')
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
                            $filtered = $records->filter(fn($record) => !empty($record->email));

                            if ($filtered->isEmpty()) {
                                Notification::make()
                                    ->title('No valid email addresses found.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            app(RichTextEmailSender::class)
                                ->sendToMany($filtered->unique('email'), $data['subject'], $data['body']);

                            Notification::make()
                                ->title('Email sent.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->modalHeading('Compose Email')
                        ->modalSubmitActionLabel('Send'),
                ]),
            ]);
    }
}
