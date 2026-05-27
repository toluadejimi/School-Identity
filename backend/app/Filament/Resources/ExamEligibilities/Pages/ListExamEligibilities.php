<?php

namespace App\Filament\Resources\ExamEligibilities\Pages;

use App\Filament\Resources\ExamEligibilities\ExamEligibilityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExamEligibilities extends ListRecords
{
    protected static string $resource = ExamEligibilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
