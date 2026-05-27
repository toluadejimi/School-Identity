<?php

namespace App\Filament\Resources\ClinicVisits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClinicVisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.full_name')->label('Student'),
                TextColumn::make('visit_type'),
                TextColumn::make('status')->badge(),
                TextColumn::make('checked_in_at')->dateTime(),
                TextColumn::make('staff.name')->label('Staff'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
