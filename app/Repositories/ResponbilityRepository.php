<?php

namespace App\Repositories;

use App\Models\Responbility;
use App\Repositories\Repository;

class ResponbilityRepository extends Repository{
    function __construct(Responbility $model){
        $this->model = $model;
    }

    function getListDataByUsername(string $username){
        $columns=[
            'responbilities.id as idResponbility',
            'categories.id as idCategory',
            'title'
        ];
        $data = $this->getAllWithInnerJoin('responbilities','categories','idCategory','id')->where('username',$username)->get($columns);
        $arrData = [];
        foreach($data as $key=>$item){
            $arrData[$key]=[
                'idTanggungJawab'=>$item->idResponbility,
                'kategori'=>$item->title
            ];
        }
        if($arrData == []){
            return false;
        }
        return $arrData;
    }

    function create(array $inputs){
        $arrAtribut = [];
        if(is_array($inputs['idKategori']) == true){
            foreach($inputs['idKategori'] as $key=>$item){
                $arrAtribut[$key]['username'] = $inputs['username'];
                $arrAtribut[$key]['idCategory'] = $item;
            }
        }else{
            $arrAtribut = [
                'username'=>$inputs['username'],
                'idCategory'=>$inputs['idKategori']
            ];
        }
        $data = $this->model->insert($arrAtribut);
        return [
            'message'=>'sukses tambah data tanggung jawab'
        ];
    }

    function deleteDataById(string $id){
        $data = $this->delete($id);
        return [
            'sukses' => $data
        ];
    }
}

?>