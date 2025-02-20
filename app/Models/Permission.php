<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends \Spatie\Permission\Models\Permission
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'parent_id',
        'type',
        'guard_name',
        'description',
        'sort',
    ];

    /**
     * @return mixed
     */
    public function parent()
    {
        return $this->belongsTo(__CLASS__, 'parent_id')->with('parent');
    }

    /**
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(__CLASS__, 'parent_id')->with('children');
    }
}