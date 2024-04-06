<?php

namespace App\Repositories\Anomaly;

use App\Models\Anomaly;
use App\Repositories\Shared\SharedRepositoryEloquent;

class AnomalyRepository extends SharedRepositoryEloquent
{
    protected Anomaly $entity;

    public function __construct(
        Anomaly $entity,
    ) {
        parent::__construct($entity);
        $this->entity = $entity;
    }

}
