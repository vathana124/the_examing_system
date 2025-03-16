<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResultResource\Pages;
use App\Filament\Resources\ExamResultResource\RelationManagers;
use App\Models\Exam;
use App\Models\ExamResult;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class ExamResultResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'ExamResult';

    protected static ?string $modelLabel = 'ExamResult';

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
                    ->label('Exam Name'),
                TextColumn::make('description')
                    ->label('Exam Description')
                    ->limit(20),
                TextColumn::make('score')
                    ->label('Exam Score')
                    ->formatStateUsing(function($record, $state){
                        return new HtmlString(
                            "<span class='text-blue-600 font-bold'>$state</span>"
                        );
                    }),
                TextColumn::make('questions')
                    ->formatStateUsing(function($record){
                        $count = DB::table('questions')->where('exam_id', $record?->id)->count();
                        return new HtmlString(
                            "<span class='text-blue-600 font-bold'>$count</span>"
                        );
                    })
                    ->label('Questions Count'),
                TextColumn::make('date')
                    ->label('Exam Date')
                    ->date(),
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
            'index' => Pages\ListExamResults::route('/'),
            'view' => Pages\ViewExamResult::route('/{record}'),
            // 'create' => Pages\CreateExamResult::route('/create'),
            // 'edit' => Pages\EditExamResult::route('/{record}/edit'),
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
