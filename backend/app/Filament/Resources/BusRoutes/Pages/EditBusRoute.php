<?php

namespace App\Filament\Resources\BusRoutes\Pages;

use App\Filament\Resources\BusRoutes\BusRouteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBusRoute extends EditRecord
{
    protected static string $resource = BusRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
