<?php

namespace App\Repositories\Role;

use App\Models\Role;
use App\Repositories\Shared\SharedRepositoryEloquent;

class RoleRepository extends SharedRepositoryEloquent
{
    private Role $entity;
    public function __construct(
        Role $entity
    ) {
        parent::__construct($entity);
        $this->entity = $entity;
    }

    public function create($data)
    {
        $data['guard_name'] = 'api';
        $role = $this->entity->create($data);

        foreach($data['permissions'] as $permissionId) {
            $role->givePermissionTo($permissionId);
        }

        return $role;
    }

    public function togglePermission($roleId, $permissionName, $activate)
    {
        $role = Role::findOrFail($roleId);
        $activate ? $role->givePermissionTo($permissionName) : $role->revokePermissionTo($permissionName);
    }
}
