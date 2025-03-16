<?php

namespace App\Models\StudentExam\Traits;

use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

trait Relationship {
  public function exam()
  {
      return $this->belongsTo(Exam::class, 'exam_id', 'id');
  }

  public function student()
  {
      return $this->belongsTo(User::class, 'user_id', 'id');
  }
}