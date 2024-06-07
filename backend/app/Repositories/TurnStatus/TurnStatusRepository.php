<?php

namespace App\Repositories\Role;

use App\Models\TurnStatus;
use App\Repositories\Shared\SharedRepositoryEloquent;

class TurnStatusRepository extends SharedRepositoryEloquent
{
    private TurnStatus $entity;
    public function __construct(
        TurnStatus $entity
    ) {
        parent::__construct($entity);
        $this->entity = $entity;
    }
}
