<?php

namespace App\Models;

use App\Models\StudentExam\Traits\Relationship;
use Illuminate\Database\Eloquent\Model;

class StudentExam extends Model
{
    use Relationship;

    protected $table = 'student_exams';
}
