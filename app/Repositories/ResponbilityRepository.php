<?php

namespace App\Repositories;

use App\Models\Responbility;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ResponbilityRepository extends Repository{
    function __construct(Responbility $model){
        $this->model = $model;
    }

    function getListDataByUsername(string $username){
        $columns=[
            'responbilities.id as idTanggungJawab',
            'nama as kategori'
        ];
        
        $checkData = $this->model->where('username',$username)->first();
        
        if(!$checkData){
            return null;
        }
        
        $table1 = ['table'=>'responbilities','key'=>'idKategori'];
        $table2 = ['table'=>'categories', 'key'=>'id'];
        $filters = ['where'=>['username'=>$username]];
        $data = $this->getAllWithInnerJoin($table1,$table2,$filters)->orderByDesc('responbilities.id')->get($columns);
        if($data->toArray() == []){
            return null;
        }
        return $data->toArray();
    }

    function create(array $inputs, string $role, string $username){
        if($role !== 'teknisi'){
            throw new Exception('gagal tambah tanggung jawab karena pegawai ini bukan teknisi');
        }
        $arrAtribut = [];
        if(is_array($inputs['idKategori']) == true){
            foreach($inputs['idKategori'] as $key=>$item){
                $arrAtribut[$key]['username'] = $username;
                $arrAtribut[$key]['idKategori'] = $item;
            }
        }else{
            $arrAtribut = [
                'username'=>auth()->payload()->get('username'),
                'idKategori'=>$inputs['idKategori']
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

    function deleteByUsername(string $username){
        $find= $this->model->where('username',$username)->first();
        if($find){
            $data = $this->model->where('username',$username)->delete();
        }
        return ['sukses'=>true];
    }
}

?>