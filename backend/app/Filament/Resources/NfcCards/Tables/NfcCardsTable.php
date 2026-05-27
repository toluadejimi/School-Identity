<?php

namespace App\Filament\Resources\NfcCards\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NfcCardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uid')->searchable()->copyable(),
                TextColumn::make('student.full_name')->label('Student')->searchable(),
                TextColumn::make('student.student_number')->label('Student No.'),
                TextColumn::make('status')->badge(),
                TextColumn::make('issued_at')->dateTime(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
