<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'module'      => 'menu',
                'permissions' => [
                    [
                        'name'                => 'menu.dashboard',
                        'display_name'        => 'dashboard',
                        'module_display_name' => 'menu',
                        'guard_name'          => 'api',
                    ],
                    [
                        'name'                => 'menu.security',
                        'display_name'        => 'seguridad',
                        'module_display_name' => 'menu',
                        'guard_name'          => 'api',
                    ],
                    [
                        'name'                => 'menu.settings',
                        'display_name'        => 'configuración',
                        'module_display_name' => 'menu',
                        'guard_name'          => 'api',
                    ],
                ],
            ],
            [
                'module'      => 'security',
                'permissions' => [
                    [
                        'name'                => 'security.users',
                        'display_name'        => 'usuarios',
                        'module_display_name' => 'seguridad',
                        'guard_name'          => 'api',
                    ],
                    [
                        'name'                => 'security.roles',
                        'display_name'        => 'roles',
                        'module_display_name' => 'seguridad',
                        'guard_name'          => 'api',
                    ],
                ],

            ],
            [
                'module'      => 'users',
                'permissions' => [
                    [
                        'name'                => 'users.list',
                        'display_name'        => 'listar',
                        'module_display_name' => 'Usuarios',
                        'guard_name'          => 'api',
                    ],
                    [
                        'name'                => 'users.create',
                        'display_name'        => 'crear',
                        'module_display_name' => 'Usuarios',
                        'guard_name'          => 'api',
                    ],
                    [
                        'name'                => 'users.edit',
                        'display_name'        => 'editar',
                        'module_display_name' => 'Usuarios',
                        'guard_name'          => 'api',
                    ],
                    [
                        'name'                => 'users.remove',
                        'display_name'        => 'eliminar',
                        'module_display_name' => 'Usuarios',
                        'guard_name'          => 'api',
                    ],
                ],
            ],
            [
                'module'      => 'roles',
                'permissions' => [
                    [
                        'name'                => 'roles.list',
                        'display_name'        => 'listar',
                        'module_display_name' => 'Roles',
                        'guard_name'          => 'api',
                    ],
                    [
                        'name'                => 'roles.create',
                        'display_name'        => 'crear',
                        'module_display_name' => 'Roles',
                        'guard_name'          => 'api',
                    ],
                    [
                        'name'                => 'roles.edit',
                        'display_name'        => 'editar',
                        'module_display_name' => 'Roles',
                        'guard_name'          => 'api',
                    ],
                    [
                        'name'                => 'roles.remove',
                        'display_name'        => 'eliminar',
                        'module_display_name' => 'Roles',
                        'guard_name'          => 'api',
                    ],
                ],
            ],
            [
                'module'      => 'settings',
                'permissions' => [
                    [
                        'name'                => 'settings.generals',
                        'display_name'        => 'Generales',
                        'module_display_name' => 'Configuraciones',
                        'guard_name'          => 'api',
                    ],
                    [
                        'name'                => 'settings.notifications',
                        'display_name'        => 'Notificaciones',
                        'module_display_name' => 'Configuraciones',
                        'guard_name'          => 'api',
                    ],
                    [
                        'name'                => 'settings.navs',
                        'display_name'        => 'Navegacion',
                        'module_display_name' => 'Configuraciones',
                        'guard_name'          => 'api',
                    ],
                    [
                        'name'                => 'settings.permissions',
                        'display_name'        => 'Permisos',
                        'module_display_name' => 'Configuraciones',
                        'guard_name'          => 'api',
                    ],
                ],
            ],
        ];

        foreach ($permissions as $permissionModule) {
            foreach ($permissionModule['permissions'] as $permission) {
                Permission::firstOrCreate($permission);
            }
        }
    }
}
