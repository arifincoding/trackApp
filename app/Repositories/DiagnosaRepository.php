<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\Diagnosa;
use App\Exceptions\Handler;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DiagnosaRepository extends Repository{
    function __construct(Diagnosa $model){
        parent::__construct($model);
    }

    function create(array $inputs,string $idService, int $confirmed=0){
        $confirm = null;
        if($confirmed === 0){
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
                'biaya'=>$item->price,
                'konfirmasi'=>$item->confirmed
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
            'idDiagnosa'=>$data->id,
            'judul'=>$data->title,
            'status'=>$data->status,
            'harga'=>$data->price,
            'konfirmasi'=>$data->confirmed
        ];
    }

    function update(array $inputs, string $id){
        $attributs = [
            'title'=> $inputs['judul'],
            'status'=> $inputs['status']
        ];

        $data = $this->save($attributs,$id);
        return [
            'idDiagnosa'=>$data->id,
            'idService'=>$data->idService
        ];
    }

    function updateStatus(array $inputs, string $id){
        $attributs = [
            'status'=>$inputs['status']
        ];
        $data = $this->save($attributs, $id);
        return [
            'idDiagnosa'=>$data->id
        ];
    }

    function updateCost(array $inputs, string $id){
        $data = $this->save(['price'=>$inputs['biaya']],$id);
        return [
            'idDiagnosa'=>$data->id,
            'idService'=>$data->idService
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