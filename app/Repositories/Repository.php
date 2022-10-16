<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\RepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class Repository implements RepositoryContract
{

    protected $model;
    protected string $modelName = "model";

    function __construct(Model $model, String $name)
    {
        $this->model = $model;
        $this->modelName = $name;
    }

    public function save(array $attribut, ?string $filter = null, string $filterName = 'id'): Model
    {
        if ($filter !== null) {
            $find = $this->model->where($filterName, $filter)->first();
            if (!$find) {
                Log::warning("update failed caused $this->modelName data by $filterName $this->modelName not found", [$filterName => $filter]);
                throw new ModelNotFoundException();
            }
        }
        $data = ($filter === null) ? $this->model->create($attribut) : $this->model->where($filterName, $filter)->update($attribut);
        if ($filter) {
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

    protected function findById(string $id, array $attributs = ['*'])
    {
        $data = $this->model->select($attributs)->find($id);
        if ($data) {
            return $data;
        }
        Log::warning("$this->modelName data by id $this->modelName not found", ["id $this->modelName" => $id]);
        throw new ModelNotFoundException();
    }

    public function delete(string $filter, string $filterName = 'id'): bool
    {
        $find = $this->model->where($filterName, $filter)->first();
        if ($find) {
            $this->model->where($filterName, $filter)->delete();
            return true;
        }
        Log::warning("delete failed caused $this->modelName data by $filterName $this->modelName not found", ["$filterName $this->modelName" => $filter]);
        throw new ModelNotFoundException();
    }
}