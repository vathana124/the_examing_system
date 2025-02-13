<?php

namespace App\Filament\Resources\CandidateListResource\Pages;

use App\Filament\Resources\CandidateListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCandidateList extends EditRecord
{
    protected static string $resource = CandidateListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
