<?php

namespace App\Repositories\Anomaly;

use App\Models\Anomaly;
use App\Repositories\Shared\SharedRepositoryEloquent;
use Illuminate\Support\Facades\DB;

class AnomalyRepository extends SharedRepositoryEloquent
{
    protected Anomaly $entity;

    public function __construct(
        Anomaly $entity,
    ) {
        parent::__construct($entity);
        $this->entity = $entity;
    }

    public function getAnomaliesByClass()
    {
        $query = $this->entity->select(
            DB::raw('count(anomalies.id) as quantity'), 
            DB::raw('anomalies.class_label as label')
        );
        
        return $query->groupBy('label')->get();
    }
}
