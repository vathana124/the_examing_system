<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends \Spatie\Permission\Models\Role
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'guard_name',
        'name',
    ];

    public const ADMIN_ID = 1;

}
