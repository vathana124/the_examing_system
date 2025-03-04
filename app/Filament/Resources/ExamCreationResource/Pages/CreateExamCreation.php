<?php

namespace App\Filament\Resources\ExamCreationResource\Pages;

use App\Filament\Resources\ExamCreationResource;
use App\Models\Exam;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class CreateExamCreation extends CreateRecord
{
    protected static string $resource = ExamCreationResource::class;

    public $questions;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Exam Info Section
                Section::make('Exam Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Exam Name')
                                    ->placeholder('e.g., English Final Exam')
                                    ->required()
                                    ->live(debounce: 500),
                                TextInput::make('duration')
                                    ->label('Duration (minutes)')
                                    ->numeric()
                                    ->placeholder('e.g., 60')
                                    ->minValue(1)
                                    ->maxValue(300)
                                    ->required(),
                                DatePicker::make('date')
                                    ->label('Exam Date')
                                    ->native(false)
                                    ->displayFormat('d F Y')
                                    ->required(),
                            ]),
                        Textarea::make('description')
                            ->label('Exam Description')
                            ->placeholder('Enter a brief description of the exam')
                            ->columnSpanFull(),
                        Toggle::make('is_prepare_exam')
                            ->label('Exam is prepared for examing.')
                            ->columnSpanFull()
                    ])
                    ->columns(2),
    
                // Questions Section
                Section::make('Questions')
                    ->schema([
                        Repeater::make('questions')
                            ->label('Question')
                            ->schema([
                                Textarea::make('question_text')
                                    ->label('Question Text')
                                    ->placeholder('Enter the question text')
                                    ->required(),
    
                                // Options Repeater
                                Repeater::make('options')
                                    ->label('Options')
                                    ->schema([
                                        TextInput::make('option_text')
                                            ->label('Option')
                                            ->placeholder('Enter an option')
                                            ->required(),
                                        Checkbox::make('is_correct')
                                            ->label('Is Correct?')
                                            ->default(false)
                                            ->inline(false),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Add Option')
                                    ->minItems(2) // At least 2 options per question
                                    ->maxItems(5), // Maximum 5 options per question
                            ])
                            ->addActionLabel('Add Question')
                            ->minItems(1) // At least 1 question per exam
                            ->maxItems(50), // Maximum 50 questions per exam
                    ])
                    ->columns(1),
            ]);
    }
    protected function getRedirectUrl(): string
    {
        return route('filament.admin.resources.exam-creations.index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->questions = $data['questions'] ?? [];
        return $data;
    }

    protected function afterCreate() {
        $exam = $this->getRecord();

        if(!empty($this->questions)){
            $result = Exam::handleCreateUpdateQuestions($this->questions, $exam);
            if(!$result){
                Notification::make()
                    ->danger()
                    ->title('Unsuccessfully !');
               return redirect()->route('filament.admin.resources.exam-creations.index');
            }
        }
    }
}
