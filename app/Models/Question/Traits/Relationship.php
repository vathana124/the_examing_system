<?php

namespace App\Models\Question\Traits;

use App\Models\Question;
use App\Models\QuestionOption;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

trait Relationship {
  public function options(){
      return $this->hasMany(QuestionOption::class, 'question_id', 'id');
  }
}