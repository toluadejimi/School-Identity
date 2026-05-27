<?php

namespace App\Filament\Resources\ExamEligibilities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExamEligibilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('exam_id')->relationship('exam', 'name')->searchable()->required(),
            Select::make('student_id')
                ->relationship('student', 'student_number')
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->student_number} - {$record->full_name}")
                ->searchable()
                ->required(),
            Toggle::make('eligible')->default(true),
            TextInput::make('reason')->maxLength(255),
        ]);
    }
}
