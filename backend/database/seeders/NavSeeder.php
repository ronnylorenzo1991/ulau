<?php

namespace Database\Seeders;

use App\Models\Nav;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class NavSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $navs = [
            [
                'name'       => 'Cuadros de Mando',
                'icon'       => "area-chart",
                'route_name' => "dashboard",
                'code'       => "dashboard",
                'permission' => "menu.dashboard",
                'class'      => null,
                'parent'     => 0,
                'nav_id'     => null,
            ],
            [
                'name'       => 'Seguridad',
                'icon'       => "lock",
                'route_name' => null,
                'code'       => 'security',
                'permission' => "menu.security",
                'class'      => null,
                'parent'     => true,
                'nav_id'     => null,
                'navs'       => [
                    [
                        'name'       => 'Usuarios',
                        'icon'       => "users",
                        'route_name' => "security/users",
                        'code'       => "security/users",
                        'permission' => "users.list",
                        'class'      => null,
                        'parent'     => 0,
                        'nav_id'     => null,
                    ],
                    [
                        'name'       => 'Roles',
                        'icon'       => "vcard",
                        'route_name' => "security/roles",
                        'code'       => "security/roles",
                        'permission' => "roles.list",
                        'class'      => null,
                        'parent'     => 0,
                        'nav_id'     => null,
                    ],
                ],
            ],
            [
                'name'       => 'Configuración',
                'icon'       => "cog",
                'route_name' => null,
                'code'       => 'settings',
                'permission' => "menu.settings",
                'class'      => null,
                'parent'     => true,
                'nav_id'     => null,
                'navs'       => [
                    [
                        'name'       => 'Generales',
                        'icon'       => "cog",
                        'route_name' => "settings/generals",
                        'code'       => "settings/generals",
                        'permission' => "settings.generals",
                        'class'      => null,
                        'parent'     => 0,
                        'nav_id'     => null,
                    ],
                    [
                        'name'       => 'Notificaciones',
                        'icon'       => "bell",
                        'route_name' => "settings/notifications",
                        'code'       => "settings/notifications",
                        'permission' => "settings.notifications",
                        'class'      => null,
                        'parent'     => 0,
                        'nav_id'     => null,
                    ],
                    [
                        'name'       => 'Navegación',
                        'icon'       => "navicon",
                        'route_name' => "settings/navigation",
                        'code'       => "settings/navigation",
                        'permission' => "settings.navs",
                        'class'      => null,
                        'parent'     => 0,
                        'nav_id'     => null,
                    ],
                    [
                        'name'       => 'Permisos',
                        'icon'       => "unlock",
                        'route_name' => "settings/permissions",
                        'code'       => "settings/permissions",
                        'permission' => "settings.permissions",
                        'class'      => null,
                        'parent'     => 0,
                        'nav_id'     => null,
                    ],
                ],
            ],
        ];
        foreach ($navs as $menu) {
            if (isset($menu['navs'])) {
                $parent = $this->createMenu($menu, true);
                $this->addPermission($parent, $menu['permission']);
                foreach ($menu['navs'] as $nav) {
                    $children = $this->createMenu($nav, false, $parent->id);
                    $this->addPermission($children, $nav['permission']);
                }
            } else {
                $nav = $this->createMenu($menu, false);
                $this->addPermission($nav, $menu['permission']);
            }
        }
    }

    private function createMenu($array, $asParent, $id = null)
    {
        $nav = Nav::where('code', '=', $array['code'])->first();
        if (empty($nav)) {
            $nav = Nav::create(
                [
                    'name'       => $array['name'],
                    'code'       => $array['code'],
                    'icon'       => $array['icon'],
                    'parent'     => $asParent ? 1 : 0,
                    'route_name' => $array['route_name'],
                    'nav_id'     => $id,
                ],
            );
        }

        return $nav;
    }

    private function addPermission($nav, $permissionName)
    {
        $permission = Permission::where('name', $permissionName)->where('guard_name', 'api')->first();

        $nav->permissions()->sync($permission->id);
    }
}
