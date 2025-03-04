<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $table = 'question_options';
    protected $fillable = [
        'option',
        'question_id',
        'is_correct',
        'created_at',
        'updated_at',
    ];
}
