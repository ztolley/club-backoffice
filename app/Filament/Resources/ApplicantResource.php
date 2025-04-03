<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantResource\Pages;
use App\Mail\RichTextEmail;
use App\Models\Applicant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;


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
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('dob')
                    ->label('DOB')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('preferred_position')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('age_groups')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('application_date')
                    ->label('Application Date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('estimated_age_group')
                    ->label('Est Group')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('saturday_club')
                    ->label('Saturday Club')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sunday_club')
                    ->label('Sunday Club')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),

                    BulkAction::make('sendEmail')
                        ->label('Send Email')
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
                            foreach ($records as $record) {
                                Mail::to($record->email)
                                    ->send(new RichTextEmail($data['subject'], $data['body']));
                            }

                            Notification::make()
                                ->title('Email sent to selected applicants.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->modalHeading('Compose Email')
                        ->modalSubmitActionLabel('Send'),

                ]),
            ])
            ->defaultSort('name', 'asc');
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
