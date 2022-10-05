<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\RepositoryContract;

class Repository implements RepositoryContract
{

    protected $model;

    function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function save(array $attribut, ?string $filter = null, string $filterName = 'id'): Model
    {
        if ($filter !== null) {
            $this->model->where($filterName, $filter)->firstOrFail();
        }
        $data = ($filter === null) ? $this->model->create($attribut) : $this->model->where($filterName, $filter)->update($attribut);
        if ($filter !== null) {
            return $this->model->where($filterName, $filter)->first();
        }
        return $data;
    }

    protected function getWhere(array $attributs = ['*'], array $filters = [], bool $withGet = true)
    {
        $query = $this->model->select($attributs)->orderByDesc('id');
        if ($filters !== []) {
            if (isset($filters['limit'])) {
                if ($filters['limit'] !== 0) {
                    $query->take($filters['limit']);
                }
            }
            if (isset($filters['where'])) {
                foreach ($filters['where'] as $key => $item) {
                    if ($item !== null) {
                        $query->where($key, $item);
                    }
                }
            }
            if (isset($filters['orWhere'])) {
                $query->where(function ($q) use ($filters) {
                    foreach ($filters['orWhere'] as $key => $item) {
                        if (is_array($item)) {
                            foreach ($item as $val) {
                                if ($val !== null) {
                                    $q->orWhere($key, $val);
                                }
                            }
                        } else {
                            if ($item !== null) {
                                $q->orWhere($key, $item);
                            }
                        }
                    }
                });
            }
            if (isset($filters['likeWhere'])) {
                $query->where(function ($q) use ($filters) {
                    foreach ($filters['likeWhere'] as $keys => $items) {
                        if ($items !== null) {
                            $q->orWhere($keys, 'LIKE', '%' . $items . '%');
                        }
                    }
                });
            }
        }
        if ($withGet === true) {
            return $query->get();
        }
        return $query;
    }

    protected function findById(string $id, array $attributs = [])
    {
        if ($attributs !== []) {
            return $this->model->select($attributs)->findOrFail($id);
        }
        return $this->model->findOrFail($id);
    }

    protected function delete(string $filter, string $filterName = 'id')
    {
        $isExist = $this->model->where($filterName, $filter)->firstOrFail();
        $data = $this->model->where($filterName, $filter)->delete();
        return true;
    }
}