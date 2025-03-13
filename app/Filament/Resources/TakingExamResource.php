<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TakingExamResource\Pages;
use App\Filament\Resources\TakingExamResource\RelationManagers;
use App\Models\Exam;
use App\Models\TakingExam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class TakingExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Taking Exam';

    protected static ?string $modelLabel = 'Taking Exam';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        //custom query
        $user = auth()->user();
        $query = Exam::where('created_by', $user?->created_by);

        return $table
            ->query($query)
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
            'index' => Pages\ListTakingExams::route('/'),
            'create' => Pages\CreateTakingExam::route('/create'),
            'edit' => Pages\EditTakingExam::route('/{record}/edit'),
        ];
    }
}
