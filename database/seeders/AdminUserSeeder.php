<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrNew(['email' => 'admin@admin.com']);
        $user->forceFill(['password' => bcrypt('6buzPH2A6XJUTHvOX6xzQr'), 'name' => 'admin'])->save();

        // Assign admin role to the user
        $role = Role::firstOrNew(['name' => config('access.role.admin')]);
        $user->roles()->sync($role->id);

        // Assign permissions to the role
        $permissions = Permission::whereNull('parent_id')->get();
        $role->givePermissionTo($permissions);
    }
}
