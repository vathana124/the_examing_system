<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\StudentExam;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        if($user->isTeacher()){
            $students = User::where('created_by', $user?->id);
            $exams = Exam::where('created_by', $user?->id);
    
            return [
                Stat::make('Total Exams', $exams->count())
                    ->description('Total exams created by '.$user?->name)
                    ->descriptionIcon('heroicon-o-document-text')
                    ->color('success'),
    
                Stat::make('Total Students', $students->count())
                    ->description('Total students registered by '.$user?->name)
                    ->descriptionIcon('heroicon-o-users')
                    ->color('primary'),
            ];
        }
        else{
            $user = auth()->user();
            $exams = Exam::where('created_by', $user?->created_by); // Use $user->id instead of $user->created_by
            $failed = 0;
            $passed = 0;
        
            foreach ($exams->get() as $exam) {
                $score = $exam->score / 2; // Calculate the passing score
                $exam_result = StudentExam::where('user_id', $user->id)
                    ->where('exam_id', $exam->id)
                    ->first();
        
                // Check if the exam result exists and compare the score
                if ($exam_result && $exam_result->score < $score) {
                    $failed += 1;
                } else {
                    $passed += 1;
                }
            }
        
            return [
                Stat::make('Total Exams', $exams->count())
                    ->description('Total exams for examing ' . $user?->name)
                    ->descriptionIcon('heroicon-o-document-text')
                    ->color('success'),
        
                Stat::make('Passed Exams', $passed)
                    ->description('passed the exams')
                    ->descriptionIcon('heroicon-o-check-circle')
                    ->color('success'),
        
                Stat::make('Failed Exams', $failed)
                    ->description('failed the exams')
                    ->descriptionIcon('heroicon-o-x-circle')
                    ->color('danger'),
            ];
        }
    }

    protected function getColumns(): int
    {
        return 2; // You can adjust the number of columns based on your layout
    }
}