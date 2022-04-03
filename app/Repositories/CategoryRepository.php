<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryRepository extends Repository{
    
    function __construct(Category $model){
        parent::__construct($model);
    }

    function saveData(array $inputs=[], string $id=null){
        $attributs = [
            'nama'=>$inputs['kategori']
        ];
        $data = $this->save($attributs,$id);
        return [
            'idKategori'=>$data->id ?? $id
        ];
    }

    function getListData(int $limit = 0, string $search = ''){
        $likeWhere = [];
        if($search !== ''){
            $likeWhere = [
                'nama'=>$search
            ];
        }
        $attributs = ['id as idKategori','nama'];
        $data = $this->getWhere($attributs,$limit,[],[],$likeWhere);
        return $data->toArray();
    }

    function getDataById(string $id){
        $attributs = ['id as idKategori','nama'];
        $data = $this->findById($id,$attributs);
        return $data->toArray();
    }

    function deleteDataById(string $id){
        $data = $this->delete($id);
        return [
            'sukses'=>$data
        ];
    }
}