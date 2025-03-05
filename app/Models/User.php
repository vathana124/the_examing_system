<?php

namespace App\Models;

use App\Models\User\Traits\Scope;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, Scope;

    public const TYPE_ADMIN = 'admin';
    public const TYPE_STUDENT = 'student';

    protected $fillable = [
        'name',
        'email',
        'password',
        'full_name',
        'birth',
        'class',
        'year',
        'phone_number',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function canAccessPanel(Panel $panel): bool{
        return true;
    }
    public function isSuperAdmin(): bool
    {
        if($this->hasRole(config('access.role.admin'))){
            return true;
        }
        return false;
    }

    public function isTeacher(): bool
    {
        if($this->hasRole(config('access.role.teacher'))){
            return true;
        }
        return false;
    }
    public function isStudent(): bool
    {
        if($this->hasRole(config('access.role.student'))){
            return true;
        }
        return false;
    }
}
