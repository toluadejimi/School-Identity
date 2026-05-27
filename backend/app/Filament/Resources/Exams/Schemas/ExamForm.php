<?php

namespace App\Filament\Resources\Exams\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class ExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            TextInput::make('subject'),
            DatePicker::make('exam_date')->required(),
            TimePicker::make('start_time'),
            TimePicker::make('end_time'),
            TextInput::make('venue'),
            Select::make('status')->options([
                'scheduled' => 'Scheduled',
                'ongoing' => 'Ongoing',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ])->default('scheduled'),
        ]);
    }
}
