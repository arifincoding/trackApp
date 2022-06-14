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
        $data = $this->model->with('kategori')->where('username',$username)->get();
        if($data->toArray() == []){
            return null;
        }
        return $data;
    }

    function create(array $inputs, string $role, string $username){
        if($role !== 'teknisi'){
            throw new Exception('gagal tambah tanggung jawab karena pegawai ini bukan teknisi');
        }
        $arrAtribut = [];
        foreach($inputs['idKategori'] as $key=>$item){
            $arrAtribut[$key]['username'] = $username;
            $arrAtribut[$key]['idKategori'] = $item;
        }
        $data = $this->model->insert($arrAtribut);
        return true;
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