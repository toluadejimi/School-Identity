<?php

namespace App\Filament\Resources\BusRoutes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BusRouteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            TextInput::make('code')->required()->unique(ignoreRecord: true),
            TextInput::make('fare_amount')->numeric()->required()->prefix('₦'),
            Select::make('status')->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
            ])->default('active'),
        ]);
    }
}
