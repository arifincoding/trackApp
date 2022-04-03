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
            'judul'=>$inputs['judul'],
            'idService'=>$idService,
            'status'=>'antri',
            'dikonfirmasi'=>$confirm,
        ];
        $data = $this->save($attributs);
        return ['idDiagnosa'=>$data->id];
    }

    function getListDataByIdService(string $idService){
        $attributs=['id as idDiagnosa','idService','judul','status','biaya','dikonfirmasi'];
        $filters = ['where'=>['idService'=>$idService]];
        $data = $this->getWhere($attributs,$filters);
        return $data->toArray();
    }

    function getDataById(string $id){
        $attributs = ['id as idDiagnosa','judul','status','biaya','dikonfirmasi'];
        $data = $this->findById($id,$attributs);
        return $data->toArray();
    }

    function update(array $inputs, string $id){
        $attributs = [
            'judul'=> $inputs['judul'],
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
        $data = $this->save(['biaya'=>$inputs['biaya']],$id);
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