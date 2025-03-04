<?php

namespace App\Filament\Resources\ExamCreationResource\Pages;

use App\Filament\Resources\ExamCreationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamCreation extends EditRecord
{
    protected static string $resource = ExamCreationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
