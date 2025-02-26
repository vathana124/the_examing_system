<?php

namespace App\Filament\Resources\UserRegistrationResource\Pages;

use App\Filament\Resources\UserRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class ListUserRegistrations extends ListRecords
{
    protected static string $resource = UserRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
                ->columns([
                    TextColumn::make('name')
                        ->label('Name'),
                    TextColumn::make('email')
                        ->label('Email'),
                    TextColumn::make('roles.name')
                        ->label('Role'),
                    TextColumn::make('created_at')
                        ->label('Created At')
                        ->dateTime('d F Y'),
                    TextColumn::make('updated_at')
                        ->label('Updated At')
                        ->dateTime('d F Y'),
                ]);
    }
}
