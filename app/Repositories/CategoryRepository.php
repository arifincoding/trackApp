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
            'title'=>$inputs['kategori']
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
                'title'=>$search
            ];
        }
        $data = $this->getWhere($limit,[],[],$likeWhere);
        
        $arrData = [];

        foreach($data as $key=>$item){
            $arrData[$key]=[
                'idKategori'=>$item->id,
                'kategori'=>$item->title
            ];
        }
        if($arrData == []){
            throw new ModelNotFoundException();
        }
        return $arrData;
    }

    function getDataById(string $id){
        $data = $this->findById($id);
        return [
            'idKategori'=>$data->id,
            'kategori'=>$data->title
        ];
    }

    function deleteDataById(string $id){
        $data = $this->delete($id);
        return [
            'sukses'=>$data
        ];
    }
}