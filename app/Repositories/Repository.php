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

    protected function getWhere(array $attributs,int $limit=0, array $where=[], array $orWhere=[],array $likeWhere=[]){
        $query = $this->model->select($attributs)->orderByDesc('id');
        
        if($limit !== 0){
            $query->take($limit);
        }
        
        if($where !== []){
            foreach($where as $key => $item){
                if($item !== null){
                    $query->where($key,$item);
                }
            }
        }
        
        if($orWhere !== []){
            $query->where(function ($q) use ($orWhere){
                foreach($orWhere as $key => $item){
                    if(is_array($item)){
                        foreach($item as $val){
                            if($val !== null){
                                $q->orWhere($key,$val);
                            }
                        }
                    }else{
                        if($item !== null){
                            $q->orWhere($key, $item);
                        }
                    }
                }
            });
            
        }
        
        if($likeWhere !==[]){

            $query->where(function ($q) use ($likeWhere){
                foreach($likeWhere as $keys=>$items){
                    if($items !== null){
                        $q->orWhere($keys,'LIKE','%'.$items.'%');
                    }
                }
            });
        }
        
        return $query->get();
    }

    protected function findById(string $id, array $attributs=[]){
        $data = $this->model;
        if($attributs !== []){
            return $data->select($attributs)->findOrFail($id);
        }
        return $data->findOrFail($id);
    }

    protected function getAllWithInnerJoin(array $table1, array $table2,int $limit=0, array $where=[], array $likeWhere=[]){
        
        $query = DB::table($table1['table'])->join($table2['table'],$table1['table'].'.'.$table1['key'],'=',$table2['table'].'.'.$table2['key']);
        
        if($limit !== 0){
            $query->take($limit);
        }

        if($where !== []){
            foreach($where as $key=>$item){
                if($item !== null){
                    $query->where($key,$item);
                }
            }
        }
        
        if($likeWhere !== []){
            $query->where(function ($q) use ($likeWhere){
                foreach($likeWhere as $key=>$item){
                    $q->orWhere($key,'LIKE','%'.$item.'%');
                }
            });
        }

        return $query;
    }

    protected function delete(string $filter, string $filterName='id'){
        $isExist = $this->model->where($filterName, $filter)->firstOrFail();
        $data = $this->model->where($filterName, $filter)->delete();
        return true;
    }
}