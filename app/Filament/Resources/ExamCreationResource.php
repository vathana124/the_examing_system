<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamCreationResource\Pages;
use App\Filament\Resources\ExamCreationResource\RelationManagers;
use App\Models\Exam;
use App\Models\ExamCreation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExamCreationResource extends Resource
{
    protected static ?string $model = Exam::class;

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
            'index' => Pages\ListExamCreations::route('/'),
            'create' => Pages\CreateExamCreation::route('/create'),
            'edit' => Pages\EditExamCreation::route('/{record}/edit'),
        ];
    }

        // acess set up

        public static function canViewAny(): bool
        {
            $user = auth()->user();
            if($user->isTeacher()){
                return true;
            }
            return false;
        }
    
        public static function canCreate(): bool
        {
            $user = auth()->user();
            if($user->isTeacher()){
                return true;
            }
            return false;
        }
    
        public static function canEdit(Model $record): bool
        {
            $user = auth()->user();
            if($user->isTeacher()){
                return true;
            }
            return false;
        }
    
        public static function canDelete(Model $record): bool
        {
            $user = auth()->user();
            if($user->isTeacher()){
                return true;
            }
            return false;
        }
    
        public static function canDeleteAny(): bool
        {
            return false;
        }
    
        public static function canForceDelete(Model $record): bool
        {
            return false;
        }
}
