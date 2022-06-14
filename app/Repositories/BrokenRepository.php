<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\Broken;
use App\Exceptions\Handler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\Formatter;

class BrokenRepository extends Repository{
    function __construct(Broken $model){
        parent::__construct($model);
    }

    function getListDataByIdService(int $idService, array $filter=[]){
        $attributs=['id as idKerusakan','judul','biaya','disetujui'];
        $filters = [
            'where'=>[
                'idService'=>$idService,
                'disetujui'=> $filter['disetujui'] ?? null
            ]
        ];
        $data = $this->getWhere($attributs,$filters);
        return $data;
    }

    function getDataById(int $id){
        $attributs = ['id as idKerusakan','idService','judul','deskripsi','biaya','disetujui'];
        $data = $this->findById($id,$attributs);
        $data->disetujui = Formatter::boolval($data->disetujui);
        $data->biayaString = Formatter::currency($data->biaya);
        return $data->toArray();
    }

    function create(array $attributs,int $idService, int $confirmed=0){
        $confirm = null;
        if($confirmed === 0){
            $confirm = true;
        }
        $attributs += [
            'idService'=>$idService,
            'disetujui'=>$confirm,
        ];
        $data = $this->save($attributs);
        return ['idKerusakan'=>$data->id];
    }

    function update(array $attributs, int $id){
        $data = $this->save($attributs,$id);
        return [
            'idKerusakan'=>$data->id,
            'idService'=>$data->idService
        ];
    }

    function setCostInNotAgreeToZero(int $idService){
        $data = $this->model->where('idService',$idService)->where('disetujui',0)->update(['biaya'=>0]);
    }

    function deleteById(int $id){
        $data = $this->delete($id);
        return[
            'sukses'=>$data
        ];
    }

    function deleteByIdService(int $id){
        $find = $this->model->where('idService',$id)->first();
        if($find){
            $data = $this->model->where('idService',$id)->delete();
        }
        return ['sukses'=>true];
    }
}

?>