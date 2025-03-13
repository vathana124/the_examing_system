<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RunPermissionsToRoles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', config('access.role.teacher'))->first();
        if($role){
            $permissions = Permission::whereNotNull('parent_id')->get();
            $role->givePermissionTo($permissions);
        }

        $role = Role::where('name', config('access.role.student'))->first();
        if($role){
            $permissions = Permission::whereNotNull('parent_id')->get();
            $role->givePermissionTo($permissions);
        }
    }
}
