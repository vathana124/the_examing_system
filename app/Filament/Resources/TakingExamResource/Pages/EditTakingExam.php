<?php

namespace App\Filament\Resources\TakingExamResource\Pages;

use App\Filament\Resources\TakingExamResource;
use App\Models\Exam;
use App\Models\StudentExam;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Throwable;

class EditTakingExam extends EditRecord
{
    protected static string $resource = TakingExamResource::class;

    public $remainingSeconds = 0;

    // global user

    public $user;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    public function mount(int | string $record): void
    {
        $this->record = Exam::where('id', $record)->first();

        // to caculate to find seconds
        $mins = $this->record?->duration;
        $seconds = $mins * 60;
        $this->remainingSeconds = $seconds;

        // get user

        $this->user = auth()->user();

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('Submit'))
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
            ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = ' . Js::from($this->previousUrl ?? static::getResource()::getUrl()) . ')')
            ->color('gray');
    }

    public function getHeader(): ?View
    {
        return null;
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $questions = $record->questions ?? null;
        if($questions){
            foreach($questions as $index => $question){
                $options = $question->options ?? null;
                $data['questions'][$index]['question_text'] = $question?->question_text . ' (' . $question?->score . 'pt)';
                if($options){
                    foreach($options as $ind => $option){
                        $data['questions'][$index]['options'] [$option?->id]=$option?->option;
                        $data['questions'][$index]['question_id']=$question?->id;
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
                                    Placeholder::make('name')
                                        ->label('Subject')
                                        ->content(function($state){
                                            return $state;
                                        }),
                                    Placeholder::make('date')
                                        ->label('Exam Date')
                                        ->content(function($state){
                                            return Carbon::createFromDate($state)->format('F j, Y') ?? $state;
                                        }),
                                    Placeholder::make('examer')
                                        ->label('Examer')
                                        ->content(function($state){
                                            return auth()->user()?->name;
                                        }),
                                    Placeholder::make('timer')
                                        ->label('Time left')
                                        ->content(fn() => sprintf('%02d:%02d', floor($this->remainingSeconds / 60), floor($this->remainingSeconds) % 60))
                                        ->extraAttributes([
                                            'class' => 'text-left font-bold text-lg',
                                            'x-data' => "{
                                                remainingSeconds: {$this->remainingSeconds},
                                                timer: null,
                                                init() {
                                                    this.startTimer();
                                                    this.\$watch('remainingSeconds', value => {
                                                        if (value <= 0) {
                                                            this.stopTimer();
                                                            this.hasExpired = true;
                                                            this.\$dispatch('otp-expired');
                                                            Livewire.dispatch('otp-expired');
                                                        }
                                                    });
                                                },
                                                startTimer() {
                                                    if (this.remainingSeconds > 0) {
                                                        this.timer = setInterval(() => {
                                                            if (this.remainingSeconds > 0) {
                                                                this.remainingSeconds--;
                                                            }
                                                        }, 1000);
                                                    }
                                                },
                                                stopTimer() {
                                                    if (this.timer) {
                                                        clearInterval(this.timer);
                                                        this.timer = null;
                                                    }
                                                },
                                                restartTimer(newSeconds) {
                                                    this.stopTimer();
                                                    this.remainingSeconds = parseInt(newSeconds) || parseInt({$this->remainingSeconds});
                                                    this.hasExpired = false;
                                                    this.startTimer();
                                                },
                                                formatTime() {
                                                    const minutes = Math.floor(this.remainingSeconds / 60).toString().padStart(2, '0');
                                                    const seconds = Math.floor(this.remainingSeconds % 60).toString().padStart(2, '0');
                                                    return minutes + ':' + seconds;
                                                }
                                            }",
                                            'x-init' => 'init()',
                                            'x-text' => 'formatTime()',
                                            'x-bind:class' => '{
                                                "text-red-500": remainingSeconds < 30,
                                                "text-yellow-500": remainingSeconds >= 30 && remainingSeconds < 60,
                                                "text-green-500": remainingSeconds >= 60
                                            }',
                                            'wire:ignore' => true,
                                        ])
                                ])
                                ->columns(4),                                
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
                                                Radio::make('options')
                                                    ->label('')
                                                    ->required()
                                                    ->options(function($state){
                                                        return $state;
                                                    }),
                                                Hidden::make('question_id'),
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

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeAccess();

        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState(afterValidate: function () {
                $this->callHook('afterValidate');

                $this->callHook('beforeSave');
            });

            $data = $this->mutateFormDataBeforeSave($data);


            // $this->handleRecordUpdate($this->getRecord(), $data);
            // custom handle
            $this->storeExamResult($data, $this->getRecord());

            $this->callHook('afterSave');

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->rememberData();

        if ($shouldSendSavedNotification) {
            $this->getSavedNotification()?->send();
        }

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
        }
    }


    public function storeExamResult($data, $exam){
        try {
            
            $student_exam = DB::table('student_exams')->insert([
                'user_id' => $this->user?->id,
                'exam_id' => $exam?->id,
                'score' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if($student_exam){
                $student_exam = DB::table('student_exams')->where('user_id', $this->user?->id)->where('exam_id', $exam?->id)->first();
            }

            $questions = $data['questions'];


            $student_answers = [];

            $questions_of_exam = $exam->questions;

            // dd($questions_of_exam, $questions);

            if(!empty($questions)){
                foreach($questions as $index => $question){
                    foreach($questions_of_exam as $index => $question_of_exam){
                        if($question_of_exam?->id == (int)$question['question_id']){
                            $options = $question_of_exam->options;
                            $answer = [
                                'student_exam_id' => $student_exam?->id,
                                'question_id' => $question_of_exam?->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ];
                            foreach($options as $index => $option){
                                if($option?->id == (int)$question['options']){
                                    $answer['selected_option'] = $option?->id;
                                    $answer['score'] = $question_of_exam?->score;
                                    $answer['correct_option'] = $option?->is_correct;

                                    $student_answers[] = $answer;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
            }

            // create student answers
            DB::table('student_answers')->insert($student_answers);

            $full_score_of_exam = DB::table('student_answers')->where('student_exam_id', $student_exam?->id)->where('correct_option', true)->sum('score');

            //update full score
            DB::table('student_exams')
                ->where('user_id', $this->user?->id)
                ->where('exam_id', $exam?->id)
                ->update([
                    'score' => $full_score_of_exam
                ]);

            $exam_ids = json_decode($this->user?->exam_ids);
            $exam_ids[] = $exam?->id;
            $this->user->exam_ids = json_encode($exam_ids);
            $this->user->save();

            // update grades
            $student_exams = StudentExam::where('exam_id', $exam?->id)->orderBy('score', 'desc')->get();

            foreach($student_exams as $index => $student_exam){
                $student_exam->grade = $index + 1;
                $student_exam->save();
            }

            DB::commit();

            Notification::make()
                ->title('Submit Exam Success!')
                ->success()
                ->send();
            return redirect()->route('filament.admin.resources.taking-exams.index');

        } catch (\Throwable $th) {
            DB::rollBack();
            Notification::make()
                ->title('Submit Exam Fail!')
                ->danger()
                ->send();
            return redirect()->route('filament.admin.resources.taking-exams.index');
        }
    }

    protected function getRedirectUrl(): ?string
    {
        return route('filament.admin.resources.taking-exams.index');
    }
}
