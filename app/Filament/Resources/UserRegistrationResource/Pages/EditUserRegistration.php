<?php

namespace App\Filament\Resources\UserRegistrationResource\Pages;

use App\Filament\Resources\UserRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserRegistration extends EditRecord
{
    protected static string $resource = UserRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
