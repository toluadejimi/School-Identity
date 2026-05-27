<?php

namespace App\Filament\Resources\AttendanceSessions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class AttendanceSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            TextInput::make('class_name'),
            DatePicker::make('session_date')->required()->default(now()),
            TimePicker::make('start_time'),
            TimePicker::make('end_time'),
            Select::make('status')->options([
                'open' => 'Open',
                'closed' => 'Closed',
            ])->default('open'),
        ]);
    }
}
