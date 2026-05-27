<?php

namespace App\Filament\Resources\ClinicVisits\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClinicVisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('student_id')
                ->relationship('student', 'student_number')
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->student_number} - {$record->full_name}")
                ->searchable()
                ->required(),
            Select::make('status')->options([
                'checked_in' => 'Checked In',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
            ]),
            Textarea::make('symptoms'),
            Textarea::make('treatment'),
            DateTimePicker::make('checked_in_at')->default(now()),
            DateTimePicker::make('completed_at'),
        ]);
    }
}
