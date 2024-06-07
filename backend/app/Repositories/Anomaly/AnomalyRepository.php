<?php

namespace App\Repositories\Anomaly;

use App\Models\Anomaly;
use App\Repositories\Shared\SharedRepositoryEloquent;
use Carbon\Carbon;
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

    public function getAnomaliesByClass($filters)
    {
        $query = $this->entity->select(
            DB::raw('count(anomalies.id) as quantity'),
            DB::raw('anomalies.class_label as label')
        );

        if (!empty($filters['date'])) {
            $filters['date'] = explode(',', $filters['date']);
            $query->whereBetween('events.created_at', $filters['date']);
        }

        if (!empty($filters['class_label'])) {
            $query->where('anomalies.class_label', $filters['class_label']);
        }

        return $query->groupBy('label')->get();
    }
}
