<?php

namespace App\Filament\Resources\ClinicVisits\Pages;

use App\Filament\Resources\ClinicVisits\ClinicVisitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClinicVisit extends EditRecord
{
    protected static string $resource = ClinicVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
