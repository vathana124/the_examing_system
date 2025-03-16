<?php

namespace App\Filament\Resources\StudentsManagerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class StudentsExamRelationManager extends RelationManager
{
    protected static string $relationship = 'students_exam';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('title')
                //     ->required()
                //     ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
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
                TextColumn::make('student.name')
                    ->label('Student'),
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
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),

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
            
                            TextEntry::make('student.name')
                                ->label('Student')
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
}
