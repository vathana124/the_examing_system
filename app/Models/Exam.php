<?php

namespace App\Models;

use App\Models\Exam\Traits\Relationship;
use App\Models\Exam\Traits\SubFunction;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use SubFunction, Relationship;
    protected $table = 'exams';
    protected $fillable = [
        'name',
        'description',
        'date',
        'duration',
        'is_prepare_exam',
        'created_at',
        'updated_at',
    ];
}
