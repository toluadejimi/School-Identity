<?php

namespace App\Filament\Resources\ExamEligibilities;

use App\Filament\Resources\ExamEligibilities\Pages\CreateExamEligibility;
use App\Filament\Resources\ExamEligibilities\Pages\EditExamEligibility;
use App\Filament\Resources\ExamEligibilities\Pages\ListExamEligibilities;
use App\Filament\Resources\ExamEligibilities\Schemas\ExamEligibilityForm;
use App\Filament\Resources\ExamEligibilities\Tables\ExamEligibilitiesTable;
use App\Models\ExamEligibility;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExamEligibilityResource extends Resource
{
    protected static ?string $model = ExamEligibility::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ExamEligibilityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamEligibilitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamEligibilities::route('/'),
            'create' => CreateExamEligibility::route('/create'),
            'edit' => EditExamEligibility::route('/{record}/edit'),
        ];
    }
}
