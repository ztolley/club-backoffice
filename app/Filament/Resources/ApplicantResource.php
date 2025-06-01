<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantResource\Pages;
use App\Models\Applicant;
use App\Services\RichTextEmailSender;
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
                    ->rows(5)
                    ->required(),
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel(),
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
                Textarea::make('medical_conditions')
                    ->rows(5)
                    ->columnSpanFull(),
                Textarea::make('injuries')
                    ->rows(5)
                    ->columnSpanFull(),
                Textarea::make('additional_info')
                    ->rows(5)
                    ->columnSpanFull(),
                DatePicker::make('application_date')
                    ->label('Application Date')
                    ->default(now()),
                Textarea::make('notes')
                    ->rows(10)
                    ->columnSpanFull(),
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
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->recordUrl(fn($record) => route('filament.admin.resources.applicants.edit', $record))
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
                            app(RichTextEmailSender::class)
                                ->sendToMany($records, $data['subject'], $data['body']);

                            Notification::make()
                                ->title('Email sent to selected applicants.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->modalHeading('Compose Email')
                        ->modalSubmitActionLabel('Send'),
                    // Export CSV Bulk Action
                    BulkAction::make('exportCsv')
                        ->label('Export as CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            // Generate CSV content
                            $csvContent = $records->map(function ($record) {
                                return [
                                    'Name' => $record->name,
                                    'Email' => $record->email,
                                    'Phone' => $record->phone,
                                    'DOB' => $record->dob?->format('Y-m-d'),
                                    'Address' => $record->address,
                                    'School' => $record->school,
                                    'Saturday Club' => $record->saturday_club,
                                    'Sunday Club' => $record->sunday_club,
                                    'Previous Clubs' => $record->previous_clubs,
                                    'Playing Experience' => $record->playing_experience,
                                    'Preferred Position' => $record->preferred_position,
                                    'Other Positions' => $record->other_positions,
                                    'Age Groups' => $record->age_groups,
                                    'How Did You Hear' => $record->how_hear,
                                    'Medical Conditions' => $record->medical_conditions,
                                    'Injuries' => $record->injuries,
                                    'Additional Info' => $record->additional_info,
                                    'Application Date' => $record->application_date?->format('dd/mm/Y'),
                                ];
                            });

                            // Create a temporary file for the CSV
                            $fileName = 'applicants_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
                            $filePath = storage_path('app/' . $fileName);

                            // Open the file and write the CSV content
                            $file = fopen($filePath, 'w');
                            fputcsv($file, array_keys($csvContent->first())); // Add headers
                            foreach ($csvContent as $row) {
                                fputcsv($file, $row);
                            }
                            fclose($file);
                            Notification::make()
                                ->title('Applicants CSV Exported')
                                ->body('The selected applicant data has been exported as a CSV file and downloaded to your computer.')
                                ->success()
                                ->send();


                            // Return the file as a download response
                            return response()->download($filePath)->deleteFileAfterSend();
                        })
                        ->deselectRecordsAfterCompletion(),
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
