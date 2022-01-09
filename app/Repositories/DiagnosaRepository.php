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

    function create(array $attributs){
        $data = $this->save($attributs);
        return ['idDiagnosa'=>$data->id];
    }

    function getListData(string $idService){
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
}

?>