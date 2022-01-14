<?php

namespace App\Repositories;

use App\Models\Responbility;
use App\Repositories\Repository;

class ResponbilityRepository extends Repository{
    function __construct(Responbility $model){
        $this->model = $model;
    }

    function getListDataByIdUser(string $username){
        $data = $this->getAllWithInnerJoin('responbilities','categories','idCategory','id')->where('username',$username)->get();
        $arrData = [];
        foreach($data as $key=>$item){
            $arrData[$key]=[
                'idTanggungJawab'=>$item->id,
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
}

?>