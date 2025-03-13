<?php

namespace App\Filament\Resources\TakingExamResource\Pages;

use App\Filament\Resources\TakingExamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTakingExams extends ListRecords
{
    protected static string $resource = TakingExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
