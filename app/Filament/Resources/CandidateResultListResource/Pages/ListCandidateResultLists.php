<?php

namespace App\Filament\Resources\CandidateResultListResource\Pages;

use App\Filament\Resources\CandidateResultListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCandidateResultLists extends ListRecords
{
    protected static string $resource = CandidateResultListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
