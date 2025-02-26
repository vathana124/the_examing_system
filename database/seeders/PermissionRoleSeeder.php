<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Traits\DisableForeignKeys;
use Exception;

class PermissionRoleSeeder extends Seeder
{
    use DisableForeignKeys;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign keys to prevent integrity constraint errors during seeding
        $this->disableForeignKeys();

        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();

        // Add role admin
        Role::create([
            'id'    => 1,
            'name'  => config('access.role.admin'),
        ]);

        // Add role Teacher
        Role::create([
            'id'    => 2,
            'name'  => config('access.role.teacher'),
        ]);

        // Add role user
        Role::create([
            'id'    => 3,
            'name'  => config('access.role.student'),
        ]);

        $actions  = [
            'list' => 'បង្ហាញទាំងអស់',
            'create' => 'បង្កើត',
            'edit' => 'កែសម្រួល',
            'delete' => 'លុប',
            'view' => 'ពិនិត្យមើល',
        ];

        $custom_actions = [
        ];

        $resource = [
            'student_registration' => 'Student Registration',
            'candidate_lists' => 'Candidate Lists',
            'candidate_result_lists' => 'Candidate Result Lists',
        ];

        foreach ($resource as $key => $value){
            // create parent
            $parent = Permission::create([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.'.$key,
                'description' => ucfirst($value),
            ]);
            $permissions = [];
            // children permissions
            $index = 1;
            if($key == 'custom_permissions'){
                foreach ($custom_actions as $c_key => $value){
                    $permissions[]= [
                        'type' => User::TYPE_ADMIN,
                        'name' => "admin.{$key}.{$c_key}",
                        'description' => ucfirst($value),
                        'guard_name' => 'web',
                        'sort' => $index,
                        'parent_id' => $parent->id
    
                    ];
                    $index++;
                }
            }
            else{
                foreach ($actions as $c_key => $value){
                    $permissions[]= [
                        'type' => User::TYPE_ADMIN,
                        'name' => "admin.{$key}.{$c_key}",
                        'description' => ucfirst($value),
                        'guard_name' => 'web',
                        'sort' => $index,
                        'parent_id' => $parent->id
    
                    ];
                    $index++;
                }
            }
            DB::table('permissions')->insert($permissions);
        }

        $all_permissions = Permission::whereNotNull('id')->pluck('id')->toArray();
        if(!empty($all_permissions)){
            $role_has_permissions = [];
            foreach ($all_permissions as $key => $value){
                $role_has_permissions[] = [
                    'permission_id' => $value,
                    'role_id' => 1
                ];
            }
            if(!empty($role_has_permissions)){
                try{
                    DB::table('role_has_permissions')->insert($role_has_permissions);
                }
                catch(Exception){
                    
                }
            }
        }

        $this->enableForeignKeys();

    }
}
