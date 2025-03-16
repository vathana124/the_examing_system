<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Filament\Resources\ResultResource\RelationManagers;
use App\Models\Exam;
use App\Models\Result;
use App\Models\StudentExam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResultResource extends Resource
{
    protected static ?string $model = StudentExam::class;

    protected static ?string $label = 'Result';

    protected static ?string $modelLabel = 'Result';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getCustomQuery(){
        //custom query
        $user = auth()->user();
        $query = StudentExam::where('user_id',$user?->id);

        return $query;
    }

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
            ->query(self::getCustomQuery())
            ->columns([
                TextColumn::make('exam.name')
                    ->label('Exam'),
                TextColumn::make('exam.score')
                    ->label('Exam Score'),
                TextColumn::make('exam.teacher.name')
                    ->label('Teacher'),
                TextColumn::make('score')
                    ->label('Your Score')
                    ->formatStateUsing(function($record, $state){
                        $exam = $record->exam;
                        return $state. ' of ' .$exam?->score;
                    }),
                TextColumn::make('exam_id')
                    ->label('Status')
                    ->formatStateUsing(function($record){
                        $exam = $record->exam;

                        if($record?->score >= ($exam?->score)/2){
                            return 'Passed';
                        }
                        else{
                            return 'Failed';
                        }
                    })
                    ->badge()
                    ->color(function($record){
                        $exam = $record->exam;

                        if($record?->score >= ($exam?->score)/2){
                            return 'info';
                        }
                        else{
                            return 'danger';
                        }
                    })
                
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListResults::route('/'),
            // 'create' => Pages\CreateResult::route('/create'),
            // 'edit' => Pages\EditResult::route('/{record}/edit'),
        ];
    }

    // acess set up

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if($user->isStudent()){
        return true;
        }
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        if($user->isStudent()){
            return true;
        }
        return false;
    }

    public static function canDelete(Model $record): bool
    {
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
