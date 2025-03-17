<?php

namespace App\Filament\Resources\ResultResource\Pages;

use App\Filament\Resources\ResultResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Container\Attributes\DB;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\HtmlString;

class ViewResult extends ViewRecord
{
    protected static string $resource = ResultResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {

        $user = auth()->user();
        $exam = $this->getRecord()->exam;
        $user_exam = $this->getRecord();
        $questions = $exam->questions ?? null;
        if($questions){
            foreach($questions as $index => $question){
                $options = $question->options ?? null;
                $data['questions'][$index]['question_text'] = $question?->question_text . ' (' . $question?->score . 'pt)';

                $student_answer = FacadesDB::table('student_answers')->where('student_exam_id', $user_exam?->id)->where('question_id', $question?->id)->first();

                if($options){
                    foreach($options as $ind => $option){
                        if($option?->is_correct){
                            $data['questions'][$index]['is_correct_answer'] = $option?->option; 
                        }
                        if($option?->id == $student_answer?->selected_option){
                            $data['questions'][$index]['student_answer']['text'] = $option?->option;
                            $data['questions'][$index]['student_answer']['is_correct'] = $student_answer?->correct_option;
                        }
                        $data['questions'][$index]['options']['options'][$option?->id]=$option?->option;
                        $data['questions'][$index]['options']['answer'] = 1;
                    }
                }
            }
        }

        return $data;
    }

    public function form(Form $form): Form
    {
        return $form
                ->schema([
                    Section::make('Exam')
                        ->schema([
                            Fieldset::make('Exam Info')
                                ->schema([
                                    Placeholder::make('exam.name')
                                        ->label('Exam Name')
                                        ->content(function($record){
                                            $state = $record->exam?->name;
                                            return new HtmlString(
                                                "<span class='text-blue-600 font-bold text-sm'>$state</span>"
                                            );
                                        }),
                                    Placeholder::make('exam.score')
                                        ->label('Exam Score')
                                        ->content(function($record){
                                            $state = $record->exam?->score;
                                            return new HtmlString(
                                                "<span class='text-blue-600 font-bold text-sm'>$state</span>"
                                            );
                                        }),
                                    Placeholder::make('exam.teacher.name')
                                        ->label('Teacher')
                                        ->content(function($record){
                                            $state = $record->exam->teacher?->name;
                                            return new HtmlString(
                                                "<span class='text-blue-600 font-bold text-sm'>$state</span>"
                                            );
                                        }),
                                    Placeholder::make('score')
                                        ->label('Your Score')
                                        ->content(function($record){
                                            $exam = $record->exam;
                                            $exam_score = $exam?->score;
                                            $state = $record?->score;
                                            return new HtmlString(
                                                "<span class='text-green-500 font-bold text-sm'>$state</span>" .
                                                "<span class='text-gray-500'> / </span>" .
                                                "<span class='text-blue-600 font-bold text-sm'>$exam_score</span>"
                                            );
                                        }),
                                    Placeholder::make('exam_id')
                                        ->label('Status')
                                        ->content(function($record){
                                            if($record){
                                                $slot = ''; 
                                                $color = '';
                                                $icon = '';

                                                $exam = $record->exam;
                                                if ($record?->score >= ($exam?->score) / 2) {
                                                    $slot = 'Passed';
                                                } else {
                                                    $slot = 'Failed';
                                                }

                                                if ($record?->score >= ($exam?->score) / 2) {
                                                    $color = 'success'; // Use 'success' for passed status
                                                } else {
                                                    $color = 'danger'; // Use 'danger' for failed status
                                                }

                                                if ($record?->score >= ($exam?->score) / 2) {
                                                    $icon = 'heroicon-o-check-circle'; // Icon for passed status
                                                } else {
                                                    $icon = 'heroicon-o-x-circle'; // Icon for failed status
                                                }
                
                                                return view('livewire.components.custom-badge', [
                                                    'slot' => $slot,
                                                    'color' => $color,
                                                    'icon' => $icon
                                                ]);
                                            }
                                        })
                                        ->dehydrated(true),
                                        Placeholder::make('grade')
                                            ->label('Grade')
                                            ->content(function($record){
                                                $state = $record?->grade;
                                                return new HtmlString(
                                                    "<span class='text-blue-600 font-bold text-sm'>$state</span>"
                                                );
                                            }),
                                ])
                                ->columns(3),                                
                            Fieldset::make('Question')
                                    ->schema([
                                        Repeater::make('questions')
                                            ->label('')
                                            ->schema([
                                                Placeholder::make('question_text')
                                                    ->label('')
                                                    ->content(function($state){
                                                        return new HtmlString(nl2br($state));
                                                    }),
                                                Grid::make(3)
                                                    ->schema([
                                                        Placeholder::make('is_correct_answer')
                                                        ->label('Correct Answer : ')
                                                        ->content(function($state){
                                                            if($state){
                                                                $slot = $state; 
                                                                $color = 'success';
                                                                $icon = 'heroicon-o-check-circle';
                                
                                                                return view('livewire.components.custom-badge', [
                                                                    'slot' => $slot,
                                                                    'color' => $color,
                                                                    'icon' => $icon
                                                                ]);
                                                            }
                                                        }),
                                                    ]),
                                                Radio::make('options')
                                                    ->label('')
                                                    ->options(function($state){
                                                        return $state['options'];
                                                    }),
                                                Grid::make(3)
                                                    ->schema([
                                                        Placeholder::make('student_answer')
                                                        ->label('Student Answer : ')
                                                        ->content(function($state){
                                                            if($state){
                                                                $slot = $state['text']; 
                                                                $color = 'success';
                                                                $icon = 'heroicon-o-check-circle';

                                                                if(!$state['is_correct']){
                                                                    $color = 'danger';
                                                                    $icon = 'heroicon-o-x-circle'; 
                                                                }
                                
                                                                return view('livewire.components.custom-badge', [
                                                                    'slot' => $slot,
                                                                    'color' => $color,
                                                                    'icon' => $icon
                                                                ]);
                                                            }
                                                        }),
                                                    ]),
                                            ])
                                            ->addable(false) // Hide the "Add" button
                                            ->deletable(false) // Hide the "Delete" button
                                            ->disableItemCreation() // Prevent adding new items
                                            ->disableItemDeletion() // Prevent deleting items
                                            ->columns(1),
                            ])
                            ->columns(1),

                        ])
                ]);
    }

}
