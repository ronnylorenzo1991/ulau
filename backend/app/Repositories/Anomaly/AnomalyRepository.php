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
        
        if(!empty($filters['filterBy'])) {
            if ($filters['filterBy'] === 'month') {
                $query->whereBetween(
                    'date_at',
                    [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
                );
            }
            if ($filters['filterBy'] === 'week') {
                $query->whereBetween(
                    'date_at',
                    [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
                );
            }
            if ($filters['filterBy'] === 'today') {
                $query->whereBetween(
                    'date_at',
                    [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()],
                );
            }
        }
        
        return $query->groupBy('label')->get();
    }
}
