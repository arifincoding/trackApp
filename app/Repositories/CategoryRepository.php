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
        if($id !== null){
            $checkData = $this->findById($id);
        }
        $data = $this->save($attributs,$id);
        return [
            'idKategori'=>$data->id ?? $id
        ];
    }

    function getListData($limit = null, $search = ''){
        $data = $this->model->orderBy('title','asc')->where(fn ($q) =>
            $this->setQuerySearch($q, $search)
        )->take($limit)->get();
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

    private function setQuerySearch($q, $search){
        $q->where('title','LIKE','%'.$search.'%');
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