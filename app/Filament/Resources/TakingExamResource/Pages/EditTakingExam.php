<?php

namespace App\Filament\Resources\TakingExamResource\Pages;

use App\Filament\Resources\TakingExamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTakingExam extends EditRecord
{
    protected static string $resource = TakingExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
