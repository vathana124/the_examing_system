<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidateListResource\Pages;
use App\Filament\Resources\CandidateListResource\RelationManagers;
use App\Models\CandidateList;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CandidateListResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Candidate List';
    protected static ?string $modelLabel = 'Candidate List';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListCandidateLists::route('/'),
            'create' => Pages\CreateCandidateList::route('/create'),
            'edit' => Pages\EditCandidateList::route('/{record}/edit'),
        ];
    }
}
