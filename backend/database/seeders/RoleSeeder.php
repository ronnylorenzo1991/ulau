<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'desarrollo', 'permissions' => ['all']],
            ['name' => 'administrador', 'permissions' => ['all']],
            [
                'name'        => 'supervisor',
                'permissions' => [
                    'menu.dashboard',
                ],
            ],
        ];

        foreach ($roles as $key => $role) {
            $newRole = Role::firstOrCreate(['name' => $role['name'], 'guard_name' => 'api']);

            foreach ($role['permissions'] as $permission) {
                if ($permission === 'all') {
                    $allPermissions = Permission::all()->pluck('name');
                    $newRole->givePermissionTo($allPermissions);
                } elseif (str_contains($permission, '*')) {
                    $permission        = explode('.', $permission);
                    $module            = $permission[0];
                    $permissionsModule = Permission::where('name', 'like', $module . '%')->pluck('name');
                    $newRole->givePermissionTo($permissionsModule);
                } else {
                    $newRole->givePermissionTo($permission);
                }
            }
        }
    }
}
