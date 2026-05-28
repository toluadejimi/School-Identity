<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Student Profile')->schema([
                TextInput::make('student_number')->required()->unique(ignoreRecord: true),
                TextInput::make('first_name')->required(),
                TextInput::make('last_name')->required(),
                TextInput::make('email')->email(),
                TextInput::make('phone')->tel(),
                TextInput::make('address'),
                TextInput::make('guardian_name'),
                TextInput::make('guardian_phone')->tel(),
                TextInput::make('class_name'),
                TextInput::make('department'),
                TextInput::make('session'),
                TextInput::make('faculty'),
                TextInput::make('level'),
                DatePicker::make('date_of_birth'),
                Select::make('gender')->options([
                    'male' => 'Male',
                    'female' => 'Female',
                    'other' => 'Other',
                ]),
                Select::make('status')->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'suspended' => 'Suspended',
                ])->default('active')->required(),
                FileUpload::make('photo_path')
                    ->image()
                    ->disk('public')
                    ->directory('students')
                    ->visibility('public'),
            ])->columns(2),
            Section::make('Medical Information')->schema([
                TextInput::make('blood_group'),
                Textarea::make('allergies')->columnSpanFull(),
                Textarea::make('medical_notes')->columnSpanFull(),
            ]),
        ]);
    }
}
