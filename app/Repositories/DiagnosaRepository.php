<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\Diagnosa;
use App\Exceptions\Handler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class DiagnosaRepository extends Repository{
    function __construct(Diagnosa $model){
        parent::__construct($model);
    }

    function create(array $inputs,string $idService){
        $checkService = DB::table('services')->where('id',$idService)->first();
        if(!$checkService){
            throw new Exception('gagal tambah data diagnosa, data service tidak ditemukan');
        }
        $confirm = null;
        if($checkService->confirmed === 0){
            $confirm = true;
        }
        $attributs = [
            'title'=>$inputs['judul'],
            'idService'=>$idService,
            'status'=>'antri',
            'confirmed'=>$confirm,
        ];
        $data = $this->save($attributs);
        return ['idDiagnosa'=>$data->id];
    }

    function getListDataByIdService(string $idService){
        $data = $this->model->where('idService',$idService)->get();
        $arrData = [];
        foreach($data as $key=>$item){
            $arrData[$key]=[
                'idService' => $item->idService,
                'idDiagnosa' => $item->id,
                'judul'=>$item->title,
                'status'=>$item->status,
                'konfirmasi'=>$item->confirmed
            ];
        }
        if($arrData == []){
            throw new ModelNotFoundException();
        }
        return $arrData;
    }

    function update(array $inputs, string $id){
        $attributs = [
            'title'=> $inputs['judul'],
            'status'=> $inputs['status']
        ];

        $data = $this->save($attributs,$id);
        return [
            'idDiagnosa'=>$data->id
        ];
    }

    function deleteById(string $id){
        $data = $this->delete($id);
        return[
            'sukses'=>$data
        ];
    }
}

?>