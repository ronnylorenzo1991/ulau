<?php

namespace App\Repositories\Permission;

use App\Models\Permission;
use App\Repositories\Shared\SharedRepositoryEloquent;

class PermissionRepository extends SharedRepositoryEloquent
{
    private Permission $entity;
    public function __construct(
        Permission $entity,
    ) {
        parent::__construct($entity);
        $this->entity = $entity;
    }
}
