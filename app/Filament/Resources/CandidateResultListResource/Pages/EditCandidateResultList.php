<?php

namespace App\Filament\Resources\CandidateResultListResource\Pages;

use App\Filament\Resources\CandidateResultListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCandidateResultList extends EditRecord
{
    protected static string $resource = CandidateResultListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
