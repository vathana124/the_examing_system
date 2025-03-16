<?php

namespace App\Models\Exam\Traits;

use App\Models\Exam;
use App\Models\Question;
use App\Models\StudentExam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

trait Relationship {
  public function questions(){
      return $this->hasMany(Question::class, 'exam_id', 'id');
  }

  public function score()
  {
      return $this->hasMany(Question::class, 'exam_id', 'id')->sum('score');
  }

  public function students_exam()
  {
      return $this->hasMany(StudentExam::class, 'exam_id', 'id');
  }

  public function teacher()
  {
      return $this->belongsTo(User::class, 'id', 'created_by');
  }

}