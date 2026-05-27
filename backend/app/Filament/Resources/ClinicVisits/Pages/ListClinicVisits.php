<?php

namespace App\Filament\Resources\ClinicVisits\Pages;

use App\Filament\Resources\ClinicVisits\ClinicVisitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClinicVisits extends ListRecords
{
    protected static string $resource = ClinicVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
