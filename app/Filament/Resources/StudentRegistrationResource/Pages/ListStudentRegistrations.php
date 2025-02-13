<?php

namespace App\Filament\Resources\StudentRegistrationResource\Pages;

use App\Filament\Resources\StudentRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentRegistrations extends ListRecords
{
    protected static string $resource = StudentRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
