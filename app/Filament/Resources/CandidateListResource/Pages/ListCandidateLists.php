<?php

namespace App\Filament\Resources\CandidateListResource\Pages;

use App\Filament\Resources\CandidateListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCandidateLists extends ListRecords
{
    protected static string $resource = CandidateListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
