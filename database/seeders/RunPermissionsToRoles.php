<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            DB::table('role_has_permissions')->where('role_id', $role?->id)->delete();
            $permission = Permission::where('name', 'admin.access.exams')->first();
            $permissions = Permission::where('parent_id', $permission?->id)->get();
            $role->givePermissionTo($permissions);
        }
    }
}
