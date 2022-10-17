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

    public function findById(string $id, array $attributs = ['*'], bool $throwException = true)
    {
        $data = $this->model->select($attributs)->find($id);
        if ($data) {
            return $data;
        }
        Log::warning("$this->modelName data by id $this->modelName not found", ["id $this->modelName" => $id]);
        if ($throwException === true) {
            throw new ModelNotFoundException();
        }
    }

    public function delete(string $filter, string $filterName = 'id', bool $throwException = true): bool
    {
        $find = $this->model->where($filterName, $filter)->first();
        if ($find) {
            $this->model->where($filterName, $filter)->delete();
            return true;
        }
        Log::warning("delete failed caused $this->modelName data by $filterName $this->modelName not found", ["$filterName $this->modelName" => $filter]);
        if ($throwException === true) {
            throw new ModelNotFoundException();
        }
        return false;
    }
}