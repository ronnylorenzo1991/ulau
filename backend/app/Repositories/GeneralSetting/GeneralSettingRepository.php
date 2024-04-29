<?php

namespace App\Repositories\GeneralSetting;

use App\Models\GeneralSetting;
use App\Repositories\Shared\SharedRepositoryEloquent;

class GeneralSettingRepository extends SharedRepositoryEloquent
{
    private GeneralSetting $entity;
    public function __construct(
        GeneralSetting $entity,
    ) {
        parent::__construct($entity);
        $this->entity = $entity;
    }

    public function getAll($sortBy, $sortDir, $perPage, $page, $relationships = null, $filters = [])
    {
        $query = GeneralSetting::select(
            'id',
            'name',
            'value',
            'description',
            'active',
        );


        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage, ['*'], 'page', $page)->toArray();
    }

    public function create($request)
    {
        $generalSetting = [
            'name'        => $request['name'],
            'value'       => $request['value'],
            'active'      => $request['active'],
            'description' => $request['description'],
        ];

        $generalSetting = $this->entity->create($generalSetting);


        return $generalSetting;
    }

    public function update($request, $id)
    {
        $generalSetting = $this->entity->find($id);
        $generalSetting->update([
            'name'        => $request['name'],
            'value'       => $request['value'],
            'active'      => $request['active'],
            'description' => $request['description'],
        ]);

        return $generalSetting;
    }
}
