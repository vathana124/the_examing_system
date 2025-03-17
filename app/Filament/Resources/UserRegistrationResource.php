<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserRegistrationResource\Pages;
use App\Filament\Resources\UserRegistrationResource\RelationManagers;
use App\Models\Exam;
use App\Models\User;
use App\Models\UserRegistration;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserRegistrationResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'User Registration';

    protected static ?string $modelLabel = 'User Registration';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserRegistrations::route('/'),
            'create' => Pages\CreateUserRegistration::route('/create'),
            'edit' => Pages\EditUserRegistration::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = User::whereNotNull('id');
        return $query->where('created_by', auth()->user()?->id);
    }
}
