<?php

namespace App\Models\Exam\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

trait SubFunction {

    /**
     * Handle the creation of questions and options in bulk.
     *
     * @param array $questions
     * @param mixed $exam
     * @return bool
     */
    public static function handleCreateUpdateQuestions(array $questions, $exam): bool
    {
        if (empty($questions) || !$exam) {
            return false; // No questions or exam provided
        }

        DB::beginTransaction(); // Start a database transaction

        $questions_of_exam = $exam->questions ?? null;
        if($questions_of_exam){
          foreach($questions_of_exam as $question){
            $options_of_question = $question->options ?? null;
            if($options_of_question){
              foreach($options_of_question as $option){
                $option->delete();
              }
            }
            $question->delete();
          }
        }

        try {
            // Prepare questions for bulk insertion
            $questionsData = [];
            foreach ($questions as $question) {
                $questionsData[] = [
                    'exam_id' => $exam->id,
                    'question_text' => $question['question_text'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            // Insert questions in bulk
            DB::table('questions')->insert($questionsData);

            // Retrieve the IDs of the inserted questions
            $insertedQuestions = DB::table('questions')
                ->where('exam_id', $exam->id)
                ->orderBy('id')
                ->limit(count($questionsData))
                ->pluck('id')
                ->toArray();
            // Prepare options for bulk insertion
            $optionsData = [];
            foreach ($questions as $index => $question) {
                if (!empty($question['options'])) {
                    foreach ($question['options'] as $option) {
                        $optionsData[] = [
                            'question_id' => $insertedQuestions[$index],
                            'option' => $option['option_text'],
                            'is_correct' => $option['is_correct'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    }
                }
            }
            // Insert options in bulk
            if (!empty($optionsData)) {
              DB::table('question_options')->insert($optionsData);
            }
            // dd($optionsData);

            DB::commit(); // Commit the transaction
            return true;
        } catch (Throwable $th) {
            DB::rollBack(); // Rollback the transaction on error
            // Log the error for debugging
            logger()->error('Error creating questions and options: ' . $th->getMessage());
            return false;
        }
    }
}