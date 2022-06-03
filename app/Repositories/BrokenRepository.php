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

    function getListDataByIdService(string $idService){
        $attributs=['id as idKerusakan','judul','biaya','dikonfirmasi'];
        $filters = ['where'=>['idService'=>$idService]];
        $data = $this->getWhere($attributs,$filters);
        foreach($data as $item){
            $item->dikonfirmasi = Formatter::boolval($item->dikonfirmasi);
            $item->biaya = Formatter::currency($item->biaya);
        }
        return $data->toArray();
    }

    function getAllByIdService(int $idService){
        $attributs = ['judul','deskripsi','biaya','dikonfirmasi'];
        $data = $this->model->select($attributs)->where('idService',$idService)->orderByDesc('id')->get();
        foreach($data as $item){
            $item->dikonfirmasi = Formatter::boolval($item->dikonfirmasi);
            $item->biaya = Formatter::currency($item->biaya);
        }
        return $data->toArray();
    }

    function getDataById(string $id){
        $attributs = ['id as idKerusakan','idService','judul','deskripsi','biaya','dikonfirmasi'];
        $data = $this->findById($id,$attributs);
        $data->dikonfirmasi = Formatter::boolval($data->dikonfirmasi);
        $data->biayaString = Formatter::currency($data->biaya);
        return $data->toArray();
    }

    function create(array $attributs,string $idService, int $confirmed=0){
        $confirm = null;
        if($confirmed === 0){
            $confirm = true;
        }
        $attributs += [
            'idService'=>$idService,
            'dikonfirmasi'=>$confirm,
        ];
        $data = $this->save($attributs);
        return ['idKerusakan'=>$data->id];
    }

    function update(array $attributs, string $id){
        $data = $this->save($attributs,$id);
        return [
            'idKerusakan'=>$data->id,
            'idService'=>$data->idService
        ];
    }

    function deleteById(string $id){
        $data = $this->delete($id);
        return[
            'sukses'=>$data
        ];
    }

    function deleteByIdService(string $id){
        $find = $this->model->where('idService',$id)->first();
        if($find){
            $data = $this->model->where('idService',$id)->delete();
        }
        return ['sukses'=>true];
    }
}

?>