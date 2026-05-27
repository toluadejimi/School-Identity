<?php

namespace App\Filament\Resources\NfcCards;

use App\Filament\Resources\NfcCards\Pages\CreateNfcCard;
use App\Filament\Resources\NfcCards\Pages\EditNfcCard;
use App\Filament\Resources\NfcCards\Pages\ListNfcCards;
use App\Filament\Resources\NfcCards\Schemas\NfcCardForm;
use App\Filament\Resources\NfcCards\Tables\NfcCardsTable;
use App\Models\NfcCard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NfcCardResource extends Resource
{
    protected static ?string $model = NfcCard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return NfcCardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NfcCardsTable::configure($table);
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
            'index' => ListNfcCards::route('/'),
            'create' => CreateNfcCard::route('/create'),
            'edit' => EditNfcCard::route('/{record}/edit'),
        ];
    }
}
