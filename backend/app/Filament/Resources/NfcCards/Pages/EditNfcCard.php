<?php

namespace App\Filament\Resources\NfcCards\Pages;

use App\Filament\Resources\NfcCards\NfcCardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNfcCard extends EditRecord
{
    protected static string $resource = NfcCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
