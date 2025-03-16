<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TakingExamResource\Pages;
use App\Filament\Resources\TakingExamResource\RelationManagers;
use App\Models\Exam;
use App\Models\TakingExam;
use Carbon\Carbon;
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

    public static function getCustomQuery(){
        //custom query
        $user = auth()->user();
        $query = Exam::whereIn('created_by', [$user?->created_by, $user?->id])
                ->where('is_prepare_exam', true)
                ->whereNotIn('id', json_decode($user?->exam_ids) ?? []);

        return $query;
    }

    public static function table(Table $table): Table
    {

        return $table
            ->query(self::getCustomQuery())
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
                    ->label('Status')
                    ->formatStateUsing(function($state, $record){
                        $date = $record?->date;
                        if($date == Carbon::now()->format('Y-m-d')){
                            return Exam::STATUS[true];
                        }
                        return Exam::STATUS[false];
                    })
                    ->badge()
                    ->color(function($state, $record){
                        $date = $record?->date;
                        if($date == Carbon::now()->format('Y-m-d')){
                            return Exam::STATUS_COLOR[true];
                        }
                        return Exam::STATUS_COLOR[false];
                    }),
                TextColumn::make('date')
                    ->label('Exam Date')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Take Exam')
                    ->hidden(function($record){
                        $date = $record?->date;
                        if($date == Carbon::now()->format('Y-m-d')){
                            return false;
                        }
                        return true;
                    })
                    ->disabled(function($record){
                        $date = $record?->date;
                        if($date == Carbon::now()->format('Y-m-d')){
                            return false;
                        }
                        return true;
                    }),
            ])
            ->recordUrl(function($record){
                $date = $record?->date;
                if($date == Carbon::now()->format('Y-m-d')){
                    return route('filament.admin.resources.taking-exams.edit', [
                            'record' => $record?->id, // Use the record's ID or unique identifier
                    ]);
                }
                return null;
            });
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
            // 'create' => Pages\CreateTakingExam::route('/create'),
            'edit' => Pages\EditTakingExam::route('/{record}/exam'),
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
