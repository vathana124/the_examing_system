<?php

namespace App\Models;

use App\Models\Exam\Traits\Relationship;
use App\Models\Exam\Traits\Scope;
use App\Models\Exam\Traits\SubFunction;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use SubFunction, Relationship, Scope;
    protected $table = 'exams';
    protected $fillable = [
        'name',
        'description',
        'date',
        'duration',
        'is_prepare_exam',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'score',
    ];

    const IS_PENDING = 'pending';
    const IS_EXAMING = 'examing';

    const STATUS = [
        false => self::IS_PENDING,
        true => self::IS_EXAMING,
    ];

    const STATUS_COLOR= [
        false => 'warning',
        true => 'info',
    ];
}
