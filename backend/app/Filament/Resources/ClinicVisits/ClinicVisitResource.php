<?php

namespace App\Filament\Resources\ClinicVisits;

use App\Filament\Resources\ClinicVisits\Pages\CreateClinicVisit;
use App\Filament\Resources\ClinicVisits\Pages\EditClinicVisit;
use App\Filament\Resources\ClinicVisits\Pages\ListClinicVisits;
use App\Filament\Resources\ClinicVisits\Schemas\ClinicVisitForm;
use App\Filament\Resources\ClinicVisits\Tables\ClinicVisitsTable;
use App\Models\ClinicVisit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClinicVisitResource extends Resource
{
    protected static ?string $model = ClinicVisit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ClinicVisitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClinicVisitsTable::configure($table);
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
            'index' => ListClinicVisits::route('/'),
            'create' => CreateClinicVisit::route('/create'),
            'edit' => EditClinicVisit::route('/{record}/edit'),
        ];
    }
}
