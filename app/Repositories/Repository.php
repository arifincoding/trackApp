<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Repository{
    function __construct(Model $model){
        $this->model = $model;
    }

    protected function save(array $attribut, string $filter = null, string $filterName = 'id'){
        
        if($filter !== null){
            $findData = $this->model->where($filterName,$filter)->firstOrFail();
        }
        $data = ($filter === null) ? $this->model->create($attribut) : $this->model->where($filterName,$filter)->update($attribut);
        if($filter !== null){
            return $this->model->where($filterName,$filter)->first();
        }
        return $data;
    }

    protected function getAll($limit=null, array $orderBy=['id','desc']){
        $data = $this->model->orderBy($orderBy[0],$orderBy[1])->take($limit);
        return $data;
    }

    protected function findById(string $id){
        $data = $this->model->findOrFail($id);
        return $data;
    }

    protected function getAllWithInnerJoin(string $table1, string $table2, string $column1, string $column2){
        $data = DB::table($table1)->join($table2,$table1.'.'.$column1,'=',$table2.'.'.$column2);
        return $data;
    }

    protected function delete(string $filter, string $filterName='id'){
        $isExist = $this->model->where($filterName, $filter)->firstOrFail();
        $data = $this->model->where($filterName, $filter)->delete();
        return true;
    }
}