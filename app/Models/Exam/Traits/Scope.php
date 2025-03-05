<?php

namespace App\Models\Exam\Traits;

use App\Models\Question;
use App\Models\QuestionOption;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

trait Scope {

  public static function query(){
    $query = parent::query();
    $user = auth()->user();

    // check if Teacher can see only own created or updated
    if(!$user->isSuperAdmin()){
      if($user->isTeacher()){
        $query = $query->where('created_by', $user?->id)->orWhere('updated_by', $user?->id);
      }else{
        $query = $query->whereNull('id');
      }
    }

    return $query;

  }

}