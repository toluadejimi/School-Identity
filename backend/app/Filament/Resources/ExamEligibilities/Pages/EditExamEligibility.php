<?php

namespace App\Filament\Resources\ExamEligibilities\Pages;

use App\Filament\Resources\ExamEligibilities\ExamEligibilityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExamEligibility extends EditRecord
{
    protected static string $resource = ExamEligibilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
