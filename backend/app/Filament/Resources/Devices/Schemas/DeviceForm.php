<?php

namespace App\Filament\Resources\Devices\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DeviceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            TextInput::make('device_uuid')->required()->unique(ignoreRecord: true),
            Select::make('type')->options(['mobile' => 'Mobile', 'pos' => 'POS Terminal'])->default('mobile'),
            Select::make('status')->options(['active' => 'Active', 'inactive' => 'Inactive'])->default('active'),
        ]);
    }
}
