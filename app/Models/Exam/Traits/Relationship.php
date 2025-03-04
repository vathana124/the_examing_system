<?php

namespace App\Models\Exam\Traits;

use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

trait Relationship {
  public function questions(){
      return $this->hasMany(Question::class, 'exam_id', 'id');
  }
}