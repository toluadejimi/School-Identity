<?php

namespace App\Filament\Resources\NfcCards\Schemas;

use App\Models\NfcCard;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NfcCardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('uid')
                ->required()
                ->unique(ignoreRecord: true)
                ->dehydrateStateUsing(fn (?string $state) => $state ? NfcCard::normalizeUid($state) : null)
                ->helperText('Enter the NFC card UID (hex).'),
            Select::make('student_id')
                ->relationship('student', 'student_number')
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->student_number} - {$record->full_name}")
                ->searchable(['student_number', 'first_name', 'last_name'])
                ->preload(),
            Select::make('status')->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'lost' => 'Lost',
                'replaced' => 'Replaced',
            ])->default('active')->required(),
            DateTimePicker::make('issued_at')->default(now()),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }
}
