<?php

namespace App\Repositories;

use App\Models\Responbility;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ResponbilityRepository extends Repository{
    function __construct(Responbility $model, DB $query){
        $this->model = $model;
        $this->query = $query;
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
        
        $check = $this->query->table('users')->where('id',$id)->first();
        if(!$check){
            throw new ModelNotFoundException();
        }
        else if($check->role !== 'teknisi'){
            throw new Exception('gagal tambah tanggung jawab karena pegawai ini bukan teknisi');
        }
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