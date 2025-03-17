<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Filament\Resources\ResultResource\RelationManagers;
use App\Models\Exam;
use App\Models\Result;
use App\Models\StudentExam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;

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
                    ->label('Exam')
                    ->searchable(),
                TextColumn::make('exam.score')
                    ->label('Exam Score')
                    ->formatStateUsing(function($record, $state){
                        return new HtmlString(
                            "<span class='text-blue-600 font-bold'>$state</span>"
                        );
                    }),
                TextColumn::make('exam.teacher.name')
                    ->label('Teacher'),
                TextColumn::make('score')
                    ->label('Your Score')
                    ->formatStateUsing(function($record, $state){
                        $exam = $record->exam;
                        $exam_score = $exam?->score;
                        return new HtmlString(
                            "<span class='text-green500 font-bold'>$state</span>" .
                            "<span class='text-gray-500'> / </span>" .
                            "<span class='text-blue-600 font-bold'>$exam_score</span>"
                        );
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
                    ->icon(function ($record) {
                        $exam = $record->exam;
                        if ($record?->score >= ($exam?->score) / 2) {
                            return 'heroicon-o-check-circle'; // Icon for passed status
                        } else {
                            return 'heroicon-o-x-circle'; // Icon for failed status
                        }
                    })
                    ->color(function($record){
                        $exam = $record->exam;

                        if($record?->score >= ($exam?->score)/2){
                            return 'success';
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
                Tables\Actions\ViewAction::make()
                    ->label('Your Answers'),
                Action::make('view')
                ->label('View')
                ->icon('heroicon-o-eye') // Add an icon (using Heroicons)
                ->color('primary') // Set the button color
                ->close() // Add a close button to the modal/slide-over
                ->infolist([
                    Fieldset::make('Result')
                        ->schema([
                            TextEntry::make('exam.name')
                                ->label('Exam Name')
                                ->weight('bold') // Make the label bold
                                ->color('gray-500'), // Set text color
            
                            TextEntry::make('exam.score')
                                ->label('Exam Score')
                                ->formatStateUsing(function ($record, $state) {
                                    return new HtmlString(
                                        "<span class='text-blue-600 font-bold text-lg'>$state</span>"
                                    );
                                }),
            
                            TextEntry::make('exam.teacher.name')
                                ->label('Teacher')
                                ->weight('medium') // Medium font weight
                                ->color('gray-500'), // Set text color
            
                            TextEntry::make('score')
                                ->label('Your Score')
                                ->formatStateUsing(function ($record, $state) {
                                    $exam = $record->exam;
                                    $exam_score = $exam?->score;
                                    return new HtmlString(
                                        "<span class='text-green-500 font-bold text-lg'>$state</span>" .
                                        "<span class='text-gray-500'> / </span>" .
                                        "<span class='text-blue-600 font-bold text-lg'>$exam_score</span>"
                                    );
                                }),
            
                            TextEntry::make('exam_id')
                                ->label('Status')
                                ->formatStateUsing(function ($record) {
                                    $exam = $record->exam;
                                    if ($record?->score >= ($exam?->score) / 2) {
                                        return 'Passed';
                                    } else {
                                        return 'Failed';
                                    }
                                })
                                ->badge()
                                ->color(function ($record) {
                                    $exam = $record->exam;
                                    if ($record?->score >= ($exam?->score) / 2) {
                                        return 'success'; // Use 'success' for passed status
                                    } else {
                                        return 'danger'; // Use 'danger' for failed status
                                    }
                                })
                                ->icon(function ($record) {
                                    $exam = $record->exam;
                                    if ($record?->score >= ($exam?->score) / 2) {
                                        return 'heroicon-o-check-circle'; // Icon for passed status
                                    } else {
                                        return 'heroicon-o-x-circle'; // Icon for failed status
                                    }
                                }),
                        ])
                        ->columns(3)
                        ->columnSpan('full'), // Make the fieldset span full width
                ])
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
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
            'view' => Pages\ViewResult::route('/{record}'),
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
