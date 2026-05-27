<?php

namespace App\Filament\Resources\BusRoutes\Pages;

use App\Filament\Resources\BusRoutes\BusRouteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBusRoutes extends ListRecords
{
    protected static string $resource = BusRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
