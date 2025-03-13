<?php

namespace App\Models;

use App\Models\Question\Traits\Relationship;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use Relationship;
    protected $table = 'questions';
    protected $fillable = [
        'exam_id',
        'question_text',
        'score',
        'created_at',
        'updated_at',
    ];
}
