<?php

namespace App\Repositories\Nav;

use App\Models\Nav;
use App\Models\Permission;
use App\Repositories\Shared\SharedRepositoryEloquent;

class NavRepository extends SharedRepositoryEloquent
{
    private Nav $entity;
    public function __construct(
        Nav $entity,
    ) {
        parent::__construct($entity);
        $this->entity = $entity;
    }

    public function getAll($sortBy, $sortDir, $perPage, $page, $relationships = null, $filters = [])
    {
        $query = Nav::select(
            'navs.id as id',
            'navs.code',
            'navs.icon',
            'navs.name',
            'navs.route_name',
            'permissions.id as permission_id'
        )->whereNull('navs.nav_id');

        $query->leftjoin('permission_nav', 'permission_nav.nav_id', 'navs.id');
        $query->leftjoin('permissions', 'permission_nav.permission_id', 'permissions.id');

        $query->with($relationships);


        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage, ['*'], 'page', $page)->toArray();
    }

    public function create($request)
    {
        $nav = [
            'name'       => $request['name'],
            'icon'       => $request['icon'],
            'route_name' => $request['route_name'],
            'code'       => $request['code'],
            'parent'     => count($request['navs']) > 0 ? 1 : 0,
            'nav_id'     => $request['parent_id'] ?? null,
        ];

        $nav        = $this->entity->create($nav);
        $permission = Permission::find($request['permission_id']);
        $nav->permissions()->sync($permission->id);

        if (count($request['navs'])) {
            foreach ($request['navs'] as $item) {
                $subNav = [
                    'name'       => $item['name'],
                    'icon'       => $item['icon'],
                    'route_name' => $item['route_name'],
                    'code'       => $item['code'],
                    'parent'     => 0,
                    'nav_id'     => $nav->id,
                ];

                $subNav     = $this->entity->create($subNav);
                $permission = Permission::find($item['permission_id']);
                $subNav->permissions()->sync($permission->id);
            }
        }

        return $nav;
    }

    public function update($request, $id)
    {
        $nav = $this->entity->find($id);
        $nav->update([
            'name'       => $request['name'],
            'icon'       => $request['icon'],
            'route_name' => $request['route_name'],
            'code'       => $request['code'],
            'parent'     => count($request['navs']) > 0 ? 1 : 0,
            'nav_id'     => $request['parent_id'] ?? null,
        ]);

        $permission = Permission::find($request['permission_id']);
        $nav->permissions()->sync($permission->id);

        if (count($request['navs'])) {
            foreach ($request['navs'] as $item) {
                $subNav = $this->entity->find($item['id']);
                $subNav->update([
                    'name'       => $item['name'],
                    'icon'       => $item['icon'],
                    'route_name' => $item['route_name'],
                    'code'       => $item['code'],
                    'parent'     => 0,
                    'nav_id'     => $nav->id,
                ]);
                $permission = Permission::find($item['permission_id']);
                $subNav->permissions()->sync($permission->id);
            }
        }

        return $nav;
    }
}
