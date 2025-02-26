<?php

namespace App\Filament\Resources\UserRegistrationResource\Pages;

use App\Filament\Resources\UserRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserRegistrations extends ListRecords
{
    protected static string $resource = UserRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
