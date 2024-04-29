<?php

namespace App\Repositories\Shared;

use Illuminate\Database\Eloquent\Collection;

abstract class SharedRepositoryEloquent implements SharedRepositoryInterface
{
    /**@var Model $model */
    protected $model;

    public function __construct($entity)
    {
        $this->model = $entity;
    }

    public function getAll($sortBy, $sortDir, $perPage, $page, $relationships = null)
    {
        return $relationships != null
            ? $this->model->with($relationships)->orderBy($sortBy, $sortDir)->paginate($perPage, ['*'], 'page', $page)->toArray()
            : $this->model->orderBy($sortBy, $sortDir)->paginate($perPage, ['*'], 'page', $page)->toArray();
    }

    public function getList(): Collection
    {
        return $this->model::all();
    }

    public function show($id): Collection
    {
        return $this->model::findOrFail($id);
    }

    public function create($request)
    {
        return $this->model::create($request->all());
    }

    public function update($request, int $id)
    {
        $model = $this->model::findOrFail($id);

        $model->update($request->all());

        return $model;
    }

    public function delete($id): int
    {
        return $this->model->destroy($id);
    }

    public function find($id, $with = [])
    {
        return $this->model->with($with)->find($id);
    }

    public function getIdByField($field, $value)
    {
        return $this->model->where($field, $value)->first()->id;
    }

    public function updateMedia($item, $requestImage, $imageKey = null, $collection = 'images')
    {
        $media = $item->addMedia($requestImage)->toMediaCollection($collection);

        if ($imageKey) {
            $item->update([
                $imageKey => $media->original_url,
            ]);
        }

        return $media;
    }

    public function removeMedia($id, $url, $collection = 'images', $removeAll = false): void
    {
        $model = $this->model->find($id);

        $images = $model->getMedia($collection);

        if ($removeAll) {
            foreach ($images as $media) {
                $media->delete();
            }
        } else {
            foreach ($images as $media) {
                if ($media->original_url === $url) {
                    $media->delete();
                }
            }
        }
    }
}
