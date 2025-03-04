<?php

namespace App\Filament\Resources\ExamCreationResource\Pages;

use App\Filament\Resources\ExamCreationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Tables;

class ListExamCreations extends ListRecords
{
    protected static string $resource = ExamCreationResource::class;

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
                        ->label('Exam Name'),
                    TextColumn::make('description')
                        ->label('Exam Description')
                        ->limit(20),
                    TextColumn::make('duration')
                        ->label('Exam Duration')
                        ->formatStateUsing(function($state){
                            return $state. '(mn)';
                        }),
                    TextColumn::make('questions')
                        ->formatStateUsing(function($record){
                            return DB::table('questions')->where('exam_id', $record?->id)->count();
                        })
                        ->label('Questions Count'),
                    TextColumn::make('is_prepare_exam')
                        ->label('Can Exam')
                        ->formatStateUsing(function($state){
                            return $state ? 'true' : 'false';
                        })
                        ->badge()
                        ->color(function($state){
                            return $state ? 'info' : 'danger';
                        }),
                    TextColumn::make('date')
                        ->label('Exam Date')
                        ->date(),
                    // TextColumn::make('created_at')
                    //     ->label('Exam Created')
                    //     ->date(),
                    // TextColumn::make('updated_at')
                    //     ->label('Exam Updated')
                    //     ->date(),

                ])
                ->actions([
                    Tables\Actions\EditAction::make(),
                ]);
    }
}
