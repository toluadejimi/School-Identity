<?php

namespace App\Filament\Resources\Wallets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WalletForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('student_id')
                ->relationship('student', 'student_number')
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->student_number} - {$record->full_name}")
                ->searchable()
                ->required()
                ->disabledOn('edit'),
            TextInput::make('balance')->numeric()->prefix('₦')->disabled(),
            Select::make('currency')->options(['NGN' => 'NGN'])->default('NGN'),
            Select::make('status')->options([
                'active' => 'Active',
                'frozen' => 'Frozen',
            ])->default('active'),
        ]);
    }
}
