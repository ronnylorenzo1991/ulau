<?php

namespace App\Repositories\AnomalyType;

use App\Models\AnomalyType;
use App\Repositories\Shared\SharedRepositoryEloquent;

class AnomalyTypeRepository extends SharedRepositoryEloquent
{
    protected AnomalyType $entity;

    public function __construct(
        AnomalyType $entity,
    ) {
        parent::__construct($entity);
        $this->entity = $entity;
    }

    public function getAll($sortBy, $sortDir, $perPage, $page, $relationships = null, $filters = [])
    {
        $query = $this->entity->select(
            'anomaly_types.id as id',
            'anomaly_types.name as name',
        )->with($relationships);

        // FILTERS
        if (!empty($filters['name'])) {
            $query->where('anomaly_types.name','like', $filters['name'].'%');
        }

        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage, ['*'], 'page', $page)->toArray();
    }
}
