<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'exams';
    protected $fillable = [
        'name',
        'description',
        'date',
        'duration',
        'created_at',
        'updated_at',
    ];
}
